<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Configuration\Payment;
use App\Http\Helper;
use App\Enums\PaymentGateway as PaymentGatewayEnum;

class PaymentController extends Controller
{
    /**
     * Store or update payment configuration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        
        // Validate incoming request data
        $request->validate([
            'provider' => 'required|in:Stripe,Paypal,Razorpay,Cashfree',
            'client_id' => 'required|max:255',
            'client_secret' => 'required|max:255',
            'mode' => 'nullable|required_if:provider,Paypal|required_if:provider,Cashfree|in:sandbox,live'
        ],[
            'mode.required_if' => "The mode field is required."
        ]);

        try {
            if ($request->provider == PaymentGatewayEnum::CASHFREE()) {
                $request->mode = $request->mode == 'live' ? 'production' : $request->mode;
            }
            
            // Store or update payment configuration
            Payment::updateOrCreate([
                'provider' => $request->provider],[
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'mode' => $request->mode ?? "live",
                'enabled' => true
            ]);

            //  Activity
            Helper::adminActivity(auth()->user(), 'Payment', 'Update', $request->provider . ' configuration has been updated.');

            // :white_check_mark: Success response
            return response()->json([
                'message' => $request->provider." configuration has been updated successfully."
            ],200);

        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Retrieve all payment configurations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        try {

            return response()->json([
                'paymentConfigs' => Payment::get()
            ],200);

        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Disable a specific payment configuration.
     *
     * @param  \App\Models\Admin\Configuration\Payment  $payment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Payment $payment) {
        try {

            $payment->enabled = false;
            $payment->save();

            //  Activity
            Helper::adminActivity(auth()->user(), 'Payment', 'Update', $payment->provider . ' configuration has been disabled.');

            // Success Response
            return response()->json([
                'message' => $payment->provider." configuration disabled successfully."
            ],200);

        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }
}
