<?php

namespace App\Jobs\User;

use App\Enums\PaymentGateway as PaymentGatewayEnum;
use App\Http\Helper;
use App\Models\Admin\BillingDetail;
use App\Models\Admin\Configuration\Payment as PaymentConfig;
use App\Models\Billing\Transaction;
use App\Models\User;
use App\Models\Admin\Tax;
use App\Mail\Transaction\TransactionSuccessful;
use App\Mail\User\AutoRechargeFailed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoRechargeWallet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $transaction = null;
        try {
            $user = $this->user->fresh();
            Log::info('Auto recharge job started.', [
                'user_id' => $this->user->id,
            ]);

            if (!$user || !$user->auto_recharge_enabled) {
                Log::info('Auto recharge job skipped: disabled or missing user.', [
                    'user_id' => $this->user->id,
                ]);
                return;
            }

            if ($user->credits > (float) $user->auto_recharge_minimum_credit) {
                Log::info('Auto recharge job skipped: credits above threshold.', [
                    'user_id' => $user->id,
                    'credits' => $user->credits,
                    'threshold' => $user->auto_recharge_minimum_credit,
                ]);
                return;
            }

            if ($user->auto_recharge_payment_gateway !== PaymentGatewayEnum::STRIPE()) {
                Log::info('Auto recharge job skipped: unsupported gateway.', [
                    'user_id' => $user->id,
                    'gateway' => $user->auto_recharge_payment_gateway,
                ]);
                return;
            }

            $paymentConfig = PaymentConfig::where('provider', PaymentGatewayEnum::STRIPE())
                ->where('enabled', true)
                ->first();
            if (!$paymentConfig) {
                Log::warning('Auto recharge job skipped: Stripe gateway disabled.', [
                    'user_id' => $user->id,
                ]);
                return;
            }

            $user->createOrGetStripeCustomer();
            $defaultPaymentMethod = $user->defaultPaymentMethod();
            if (!$defaultPaymentMethod) {
                $this->sendFailureEmail($user, "No default payment method found. Please save a default card for Stripe auto recharge.");
                Log::warning('Auto recharge skipped due to missing default payment method.', [
                    'user_id' => $user->id
                ]);
                return;
            }
            Log::info('Auto recharge default payment method found.', [
                'user_id' => $user->id,
                'payment_method_id' => $defaultPaymentMethod->id,
            ]);

            $companyBillingDetails = BillingDetail::first();
            $billingDetail = $user->billingDetail;
            $countryCode = strtoupper((string) $user->getCountryCodeValue());
            $baseAmount = round((float) $user->auto_recharge_amount, 3);
            Log::info('Auto recharge job amount prepared.', [
                'user_id' => $user->id,
                'base_amount' => $baseAmount,
                'gateway' => PaymentGatewayEnum::STRIPE(),
            ]);

            $taxAmount = 0;
            $taxDetails = [];
            if ($companyBillingDetails && Tax::exists()) {
                $calculatedTaxDetail = Helper::calculateTaxAmount($baseAmount, $user->getCountryCodeValue(), $user->getRegionCodeValue());
                $taxDetails = $calculatedTaxDetail['tax_applied'];
                $taxAmount = $calculatedTaxDetail['tax_amount'];
            }

            $transaction = DB::transaction(function () use ($user, $baseAmount, $taxAmount, $taxDetails, $companyBillingDetails, $billingDetail) {
                return $user->transactions()->create([
                    'service' => 'Auto recharge wallet credits.',
                    'base_amount' => $baseAmount,
                    'discount_amount' => 0,
                    'tax_amount' => $taxAmount,
                    'final_amount' => $baseAmount + $taxAmount,
                    'payment_gateway' => PaymentGatewayEnum::STRIPE(),
                    'key' => Helper::generateUniqueToken(32, 'transactions', 'key'),
                    'company_address' => $companyBillingDetails ? [
                        'name' => $companyBillingDetails->name ?? null,
                        'email' => $companyBillingDetails->email ?? null,
                        'address' => $companyBillingDetails->address . ", " . $companyBillingDetails->city . ", " . $companyBillingDetails->region . ", " . $companyBillingDetails->country . ", " . $companyBillingDetails->postal_code,
                    ] : null,
                    'company_tax_numbers' => $companyBillingDetails ? $companyBillingDetails->tax_numbers : null,
                    'tax_details' => $taxDetails,
                    'address' => $billingDetail ? [
                        'name' => $billingDetail->name ?? null,
                        'email' => $billingDetail->email ?? null,
                        'address' => $billingDetail->address . ", " . $billingDetail->city . ", " . $billingDetail->region . ", " . $billingDetail->country . ", " . $billingDetail->postal_code,
                    ] : null,
                    'tax_numbers' => $billingDetail ? $billingDetail->tax_numbers : null,
                    'status' => 2,
                ])->refresh();
            });
            Log::info('Auto recharge pending transaction created.', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'transaction_key' => $transaction->key,
                'final_amount' => $transaction->final_amount,
            ]);

            $currency = Helper::currency()['currency'];
            $finalAmount = (int) round((float) $transaction->final_amount * 100);

            if (
                $countryCode === 'IN' &&
                (
                    !$billingDetail ||
                    empty($billingDetail->name) ||
                    empty($billingDetail->address) ||
                    empty($billingDetail->city) ||
                    empty($billingDetail->region) ||
                    empty($billingDetail->postal_code) ||
                    empty($billingDetail->country_code)
                )
            ) {
                $transaction->update(['status' => 0]);
                $this->sendFailureEmail($user, 'For Indian cards, billing name and complete address are required. Please update Billing Details and try again.');
                Log::warning('Auto recharge blocked: missing India export billing details.', [
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id,
                ]);
                return;
            }

            // Keep Stripe customer profile updated for compliant off-session charges.
            $billingCountryCode = strtoupper((string) (($billingDetail && $billingDetail->country_code) ? $billingDetail->country_code : $user->getCountryCodeValue()));
            $billingName = ($billingDetail && $billingDetail->name) ? $billingDetail->name : $user->name;
            $billingEmail = ($billingDetail && $billingDetail->email) ? $billingDetail->email : $user->email;
            $billingLine1 = $billingDetail ? $billingDetail->address : null;
            $billingCity = $billingDetail ? $billingDetail->city : null;
            $billingState = $billingDetail ? $billingDetail->region : null;
            $billingPostalCode = $billingDetail ? $billingDetail->postal_code : null;

            if ($billingDetail) {
                $user->updateStripeCustomer([
                    'name' => $billingName,
                    'email' => $billingEmail,
                    'address' => [
                        'line1' => $billingLine1,
                        'city' => $billingCity,
                        'state' => $billingState,
                        'postal_code' => $billingPostalCode,
                        'country' => $billingCountryCode,
                    ],
                ]);
            }

            Log::info('Auto recharge Stripe charge initiated.', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'charge_amount_minor' => $finalAmount,
                'currency' => $currency,
            ]);
            $payment = $user->stripe()->paymentIntents->create([
                'amount' => $finalAmount,
                'currency' => $currency,
                'customer' => $user->stripe_id,
                'payment_method' => $defaultPaymentMethod->id,
                'off_session' => true,
                'confirm' => true,
                'description' => 'Auto recharge wallet credits',
                'receipt_email' => $user->email,
                'shipping' => [
                    'name' => $billingName,
                    'address' => [
                        'line1' => $billingLine1,
                        'city' => $billingCity,
                        'state' => $billingState,
                        'postal_code' => $billingPostalCode,
                        'country' => $billingCountryCode,
                    ],
                ],
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ]);

            if (!in_array($payment->status, ['succeeded', 'processing'])) {
                $transaction->update(['status' => 0]);
                $this->sendFailureEmail($user, "Auto recharge payment failed with status: {$payment->status}.");
                Log::warning('Auto recharge payment intent did not succeed.', [
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id,
                    'payment_intent_id' => $payment->id ?? null,
                    'payment_status' => $payment->status ?? null,
                ]);
                return;
            }

            $transaction->update([
                'status' => 1,
                'transaction_id' => $payment->id ?? null,
                'payment_link' => null,
                'paid_at' => now(),
            ]);

            $user->credits = $user->credits + $transaction->base_amount;
            $user->save();
            Log::info('Auto recharge credits updated.', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'added_credits' => $transaction->base_amount,
                'current_credits' => $user->credits,
            ]);

            \Mail::to($transaction->user)->queue((new TransactionSuccessful($transaction))->onQueue('default'));

            Log::info('Auto recharge completed successfully.', [
                'user_id' => $user->id,
                'transaction_id' => $transaction->id,
                'gateway' => $transaction->payment_gateway,
                'final_amount' => $transaction->final_amount,
            ]);
        } catch (\Exception $e) {
            report($e);
            if ($transaction) {
                $transaction->update(['status' => 0]);
            }
            $this->sendFailureEmail($this->user, $e->getMessage());
            Log::error('Auto recharge job failed.', [
                'user_id' => $this->user->id,
                'message' => $e->getMessage(),
            ]);
            $this->fail("Auto recharge failed for user ({$this->user->id}): " . $e->getMessage());
        }
    }

    protected function sendFailureEmail(User $user, string $reason): void
    {
        \Mail::to($user)->queue((new AutoRechargeFailed($user, $reason))->onQueue('default'));
    }
}
