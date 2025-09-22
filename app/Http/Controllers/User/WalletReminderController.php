<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helper;

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
}
