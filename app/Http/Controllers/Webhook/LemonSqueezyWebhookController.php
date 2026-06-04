<?php

namespace App\Http\Controllers\Webhook;

use Illuminate\Http\Request;
use App\Models\Billing\Transaction;
use App\Models\Admin\Configuration\Payment as PaymentConfig;
use App\Enums\PaymentGateway as PaymentGatewayEnum;
use App\Http\Controllers\User\TransactionController;
use App\Mail\Transaction\TransactionSuccessful;
use App\Models\Billing\PromoCode;
use App\Http\Helper;

class LemonSqueezyWebhookController
{
    public function handle(Request $request)
    {
        $paymentConfig = PaymentConfig::where('provider', PaymentGatewayEnum::LEMONSQUEEZY())
            ->where('enabled', true)
            ->first();

        if (!$paymentConfig) {
            return response()->json(['message' => 'Lemonsqueezy not configured.'], 200);
        }

        // Verify HMAC signature
        $signature = $request->header('X-Signature');
        $payload   = $request->getContent();
        $expected  = hash_hmac('sha256', $payload, $paymentConfig->client_secret);

        if (!hash_equals($expected, (string) $signature)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $data      = $request->json()->all();
        $eventName = $data['meta']['event_name'] ?? null;

        if ($eventName !== 'order_created') {
            return response()->json(['message' => 'Event ignored.'], 200);
        }

        $attributes     = $data['data']['attributes'] ?? [];
        $transactionKey = $data['meta']['custom_data']['transaction_key'] ?? null;

        if (!$transactionKey || ($attributes['status'] ?? null) !== 'paid') {
            return response()->json(['message' => 'Order not paid or missing transaction key.'], 200);
        }

        $transaction = Transaction::where('key', $transactionKey)
            ->where('status', 2)
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found or already processed.'], 200);
        }

        $transaction->status         = 1;
        $transaction->payment_link   = null;
        $transaction->transaction_id = (string) ($data['data']['id'] ?? '');
        $transaction->save();

        app(TransactionController::class)->updateAccount($transaction);

        // Update promo code usage
        if ($promoCode = PromoCode::find($transaction->promo_code_id)) {
            $promoCode->usage++;
            if ($promoCode->usable) {
                $promoCode->expires_at = $promoCode->usable <= $promoCode->usage ? now() : $promoCode->expires_at;
            }
            $promoCode->save();
        }

        \Mail::to($transaction->user)->queue((new TransactionSuccessful($transaction))->onQueue('default'));

        $currency = Helper::currency();
        Helper::createActivity(
            $transaction->user,
            'Transaction',
            'Create',
            'Transaction of ' . $currency['currency_symbol'] . $transaction->final_amount . ' has been created.'
        );

        return response()->json(['message' => 'Webhook processed successfully.'], 200);
    }
}
