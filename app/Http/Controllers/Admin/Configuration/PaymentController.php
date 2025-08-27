<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Configuration\Payment;
use App\Http\Helper;

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
            'provider' => 'required|in:Stripe,Paypal,Razorpay,Paytr',
            'client_id' => 'required|max:255',
            'client_secret' => 'required|max:255',
            'client_key' => 'nullable|required_if:provider,Paytr',
            'mode' => 'nullable|required_if:provider,Paypal|in:sandbox,live'
        ],[
            'mode.required_if' => "The mode field is required.",
            'client_key.required_if' => "The client key field is required."
        ]);

        try {
            // Store or update payment configuration
            Payment::updateOrCreate([
                'provider' => $request->provider],[
                'client_id' => $request->client_id,
                'client_secret' => $request->client_secret,
                'client_key' => $request->client_key,
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
