<?php

namespace App\Http\Controllers\Admin\Server;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Server\{Server, Subscription};
use App\Models\User;
use App\Http\Helper;

class SubscriptionController extends Controller
{
    /**
     * Update the subscription details for a specific server and user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @param  \App\Models\Server\Server  $server
     * @param  \App\Models\Server\Subscription  $subscription
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user, Server $server, Subscription $subscription){
        // Validate the incoming request data
        $request->validate([
            "amount" => "required|numeric",
        ]);

        try {
            // Check if the server is associated with the user
            if(!$user->servers()->find($server->id)){
                return response()->json([
                    "message" => "Server is not associated with the user!"
                ],500);
            }

            // Verify that the subscription belongs to the specified server
            if($server->subscription->id != $subscription->id){
                return response()->json([
                    "message" => "Subscription is not associated with the server!"
                ],500);
            }

            $currency = Helper::currency();
            
            // Store Admin Activity
            Helper::adminActivity(auth()->user(), 'Subscription', 'Update', 'Subscription price updated from ' . $currency['currency_symbol'] . $subscription->monthly_price . ' to ' . $currency['currency_symbol'] . $request->amount . '.');

            // Update the subscription price with the new amount
            $subscription->update(["monthly_price" => $request->amount]);

            // Return a success response: subscription updated successfully.
            return response()->json([
                "message" => "Subscription updated successfully."
            ],200);
        } catch (\Exception $e) {
            // Log the exception and return JSON response with error message
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }
}
