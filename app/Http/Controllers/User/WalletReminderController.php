<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helper;
use App\Models\Admin\Configuration\Payment as PaymentConfig;
use App\Models\Admin\BillingDetail;
use App\Enums\PaymentGateway as PaymentGatewayEnum;
use App\Rules\AmountFormatRule;
use Illuminate\Support\Facades\Log;

class WalletReminderController extends Controller
{
    /**
     * Update the user's reminder minimum credit.
     */
    public function reminderCredit(Request $request)
    {
        $request->validate([
            'reminder_minimum_credit' => ['nullable', 'numeric', 'min:1', 'max:500'],
        ]);

        try {
            $user = auth()->user();
            
            // Get the new reminder credit value from the request
            $newReminderCredit = $request->reminder_minimum_credit;

            // Update the user's reminder minimum credit
            $user->update(['reminder_minimum_credit' => $newReminderCredit]);

            // Create an activity record
            Helper::createActivity($user, 'Billing', 'Update', 'Credit reminder updated.');

            // Success response
            return response()->json([
                'message' => 'Credit Reminder updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            // Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ], 500);
        }
    }

    /**
     * Update the user's auto recharge settings.
     */
    public function autoRecharge(Request $request)
    {
        $enabledProviders = PaymentConfig::where('enabled', true)->pluck('provider')->toArray();

        $minimumCredits = 1;
        $companyBillingDetails = BillingDetail::first();
        if ($companyBillingDetails && $companyBillingDetails->minimum_recharge_amount > 0) {
            $minimumCredits = (double) $companyBillingDetails->minimum_recharge_amount;
        }

        $request->validate([
            'auto_recharge_enabled' => ['required', 'boolean'],
            'auto_recharge_minimum_credit' => ['nullable', 'numeric', 'min:1', 'max:5000', new AmountFormatRule],
            'auto_recharge_amount' => ['nullable', 'numeric', 'min:' . $minimumCredits, new AmountFormatRule],
            'auto_recharge_payment_gateway' => ['nullable', 'in:' . implode(",", $enabledProviders)],
        ]);

        try {
            $user = auth()->user();

            $enabled = (bool) $request->auto_recharge_enabled;
            $gateway = $request->auto_recharge_payment_gateway;

            if ($enabled) {
                $missingErrors = [];
                if (empty($request->auto_recharge_minimum_credit)) {
                    $missingErrors['auto_recharge_minimum_credit'] = ['Minimum threshold is required when auto recharge is enabled.'];
                }
                if (empty($request->auto_recharge_amount)) {
                    $missingErrors['auto_recharge_amount'] = ['Recharge amount is required when auto recharge is enabled.'];
                }
                if (empty($gateway)) {
                    $missingErrors['auto_recharge_payment_gateway'] = ['Payment gateway is required when auto recharge is enabled.'];
                }

                if (!empty($missingErrors)) {
                    return response()->json([
                        'message' => 'Please fill all required auto recharge fields.',
                        'errors' => $missingErrors,
                    ], 422);
                }

                if ($gateway !== PaymentGatewayEnum::STRIPE()) {
                    return response()->json([
                        'message' => 'Auto recharge currently supports Stripe only.',
                        'errors' => [
                            'auto_recharge_payment_gateway' => ['Auto recharge currently supports Stripe only.'],
                        ]
                    ], 422);
                }

                // Billing details API already validates required fields, so only ensure it exists.
                if (strtoupper((string) $user->getCountryCodeValue()) === 'IN') {
                    if (!$user->billingDetail) {
                        return response()->json([
                            'message' => 'Billing details are required before enabling auto recharge.',
                            'errors' => [
                                'auto_recharge_enabled' => [
                                    'Please add billing details first.',
                                ],
                            ],
                        ], 422);
                    }
                }
            }

            $user->update([
                'auto_recharge_enabled' => $enabled,
                'auto_recharge_minimum_credit' => $enabled ? $request->auto_recharge_minimum_credit : null,
                'auto_recharge_amount' => $enabled ? $request->auto_recharge_amount : null,
                'auto_recharge_payment_gateway' => $enabled ? $gateway : null,
            ]);

            Helper::createActivity($user, 'Billing', 'Update', 'Auto recharge settings updated.');

            return response()->json([
                'message' => 'Auto recharge settings updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ], 500);
        }
    }

    /**
     * Create a Stripe setup checkout session for saving default payment method.
     */
    public function createAutoRechargeSetupSession(Request $request)
    {
        $request->validate([
            'gateway' => ['required', 'in:' . PaymentGatewayEnum::STRIPE()],
        ]);

        try {
            $user = auth()->user();

            $isStripeEnabled = PaymentConfig::where('provider', PaymentGatewayEnum::STRIPE())
                ->where('enabled', true)
                ->exists();
            if (!$isStripeEnabled) {
                return response()->json([
                    'message' => 'Stripe payment gateway is currently disabled.',
                ], 422);
            }

            $user->createOrGetStripeCustomer();
            $session = $user->stripe()->checkout->sessions->create([
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card'],
                'mode' => 'setup',
                'billing_address_collection' => 'required',
                'customer_update' => [
                    'name' => 'auto',
                    'address' => 'auto',
                ],
                'success_url' => url(config('app.url') . '/billing/auto-recharge?setup_status=success&session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => url(config('app.url') . '/billing/auto-recharge?setup_status=cancelled'),
                'metadata' => [
                    'user_id' => $user->id,
                    'flow' => 'auto_recharge_default_payment_method',
                ],
            ]);

            return response()->json([
                'url' => $session->url,
                'message' => 'Redirecting you to Stripe to save payment method.',
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => $e->getMessage() ? $e->getMessage() : "Something went really wrong!",
            ], 500);
        }
    }

    /**
     * Verify setup session and set default payment method.
     */
    public function verifyAutoRechargeSetupSession(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        try {
            $user = auth()->user();
            $user->createOrGetStripeCustomer();

            $session = $user->stripe()->checkout->sessions->retrieve($request->session_id);
            if (($session->status ?? null) !== 'complete' || empty($session->setup_intent)) {
                return response()->json([
                    'message' => 'Payment method setup is not completed yet.',
                ], 422);
            }

            $setupIntent = $user->stripe()->setupIntents->retrieve($session->setup_intent);
            if (($setupIntent->status ?? null) !== 'succeeded') {
                return response()->json([
                    'message' => 'Payment method setup was not successful.',
                ], 422);
            }

            $paymentMethodId = is_string($setupIntent->payment_method)
                ? $setupIntent->payment_method
                : ($setupIntent->payment_method->id ?? null);

            if (!$paymentMethodId) {
                return response()->json([
                    'message' => 'Unable to find saved payment method.',
                ], 422);
            }

            $user->updateDefaultPaymentMethod($paymentMethodId);

            Helper::createActivity($user, 'Billing', 'Update', 'Default payment method updated for auto recharge.');

            return response()->json([
                'message' => 'Default payment method saved successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);
            Log::error('Unable to verify Stripe setup session for auto recharge.', [
                'user_id' => auth()->id(),
                'session_id' => $request->session_id,
                'message' => $e->getMessage(),
            ]);
            return response()->json([
                "message" => $e->getMessage() ? $e->getMessage() : "Something went really wrong!",
            ], 500);
        }
    }

    /**
     * Return user's default payment method status for auto recharge.
     */
    public function autoRechargePaymentMethodStatus()
    {
        try {
            $user = auth()->user();
            $user->createOrGetStripeCustomer();
            $defaultPaymentMethod = $user->defaultPaymentMethod();
            $paymentMethods = $user->paymentMethods();

            return response()->json([
                'has_default_payment_method' => (bool) $defaultPaymentMethod,
                'default_payment_method' => $defaultPaymentMethod ? [
                    'id' => $defaultPaymentMethod->id,
                    'brand' => $defaultPaymentMethod->card->brand ?? null,
                    'last4' => $defaultPaymentMethod->card->last4 ?? null,
                    'exp_month' => $defaultPaymentMethod->card->exp_month ?? null,
                    'exp_year' => $defaultPaymentMethod->card->exp_year ?? null,
                ] : null,
                'payment_methods' => collect($paymentMethods)->map(function ($paymentMethod) use ($defaultPaymentMethod) {
                    return [
                        'id' => $paymentMethod->id,
                        'brand' => $paymentMethod->card->brand ?? null,
                        'last4' => $paymentMethod->card->last4 ?? null,
                        'exp_month' => $paymentMethod->card->exp_month ?? null,
                        'exp_year' => $paymentMethod->card->exp_year ?? null,
                        'is_default' => $defaultPaymentMethod ? $defaultPaymentMethod->id === $paymentMethod->id : false,
                    ];
                })->values(),
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ], 500);
        }
    }

    /**
     * Remove user's default payment method for auto recharge.
     */
    public function removeAutoRechargeDefaultPaymentMethod()
    {
        try {
            $user = auth()->user();
            $user->createOrGetStripeCustomer();
            $paymentMethodId = request()->input('payment_method_id');
            if (!$paymentMethodId) {
                return response()->json([
                    'message' => 'Payment method is required.',
                    'errors' => [
                        'payment_method_id' => ['Payment method is required.'],
                    ],
                ], 422);
            }

            $paymentMethod = $user->stripe()->paymentMethods->retrieve($paymentMethodId, []);
            if (!$paymentMethod) {
                return response()->json([
                    'message' => 'Payment method not found.',
                ], 404);
            }

            if (($paymentMethod->customer ?? null) !== $user->stripe_id) {
                return response()->json([
                    'message' => 'Payment method not found.',
                ], 404);
            }

            $defaultPaymentMethod = $user->defaultPaymentMethod();
            if ($defaultPaymentMethod && $defaultPaymentMethod->id === $paymentMethodId) {
                return response()->json([
                    'message' => 'Default payment method cannot be deleted.',
                ], 422);
            }

            $user->deletePaymentMethod($paymentMethodId);

            Helper::createActivity($user, 'Billing', 'Update', 'Default payment method removed for auto recharge.');

            return response()->json([
                'message' => 'Payment method removed successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ], 500);
        }
    }

    /**
     * Set user's default payment method for auto recharge.
     */
    public function setAutoRechargeDefaultPaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method_id' => ['required', 'string'],
        ]);

        try {
            $user = auth()->user();
            $user->createOrGetStripeCustomer();

            $paymentMethod = $user->stripe()->paymentMethods->retrieve($request->payment_method_id, []);
            if (!$paymentMethod) {
                return response()->json([
                    'message' => 'Payment method not found.',
                ], 404);
            }

            if (($paymentMethod->customer ?? null) !== $user->stripe_id) {
                return response()->json([
                    'message' => 'Payment method not found.',
                ], 404);
            }

            $user->updateDefaultPaymentMethod($request->payment_method_id);

            Helper::createActivity($user, 'Billing', 'Update', 'Default payment method changed for auto recharge.');

            return response()->json([
                'message' => 'Default payment method updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ], 500);
        }
    }
}
