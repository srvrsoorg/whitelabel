<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Helper;
use DB;

class AuthController extends Controller
{
    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        try {
            // Get the authenticated user
            $user = auth()->user();

            // Return user details in JSON response
            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {
            // ❌ Error response for exceptions
            report($e);
            return response()->json(['message' => 'Failed to fetch user details!'], 500);
        }
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'regex:/^[\p{L}\s]+$/u', 'min:3', 'max:255'],
            'country_name' => 'required|string',
            'country_code' => 'required|string',
            'region_name' => 'required|string',
            'region_code' => 'required|string',
            'timezone' => 'required|string',
        ]);

        try {
            // Get the authenticated user
            $user = auth()->user();

            $user->fill($validatedData)->save();

            // Log activity
            Helper::createActivity($user, 'Account', 'Update', 'Profile details have been updated.');

            // ✅ Fire webhook directly here
            app(\App\Services\WebhookService::class)->send('User', 'Updated', [
                'user' => [
                    'id'           => $user->id,
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'country_name' => $user->country_name,
                    'region_name'  => $user->region_name,
                    'timezone'     => $user->timezone,
                    'updated_at'   => $user->updated_at,
                ]
            ]);

            // ✅ Success response
            return response()->json([
                'message' => 'Profile has been updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e->getMessage());
            // ❌ Error response for exceptions
            return response()->json(['message' => 'Failed to update profile!'], 500);
        }
    }

    /**
     * Change the existing user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'current_password'=> [
                'required',
                'min:8',
                'max:32',
                function ($attribute, $value, $fail) {
                    // Check if the provided password matches the user's current password
                    if (!Hash::check($value, auth()->user()->password)) {
                        $fail("The current password you entered is incorrect.");
                    }
                },
            ],
            'new_password'=>'required|min:8|max:32|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,32}$/|confirmed|different:current_password',
        ],[
            'new_password.regex' => 'The new password must contain at least one uppercase letter, one digit, and one special character (@$!%*?&).',
            'new_password.different' => 'The new password must be different from your current password.',
        ]);

        try {
            // Get the authenticated user
            $user = auth()->user();

            // Update the user's password with the new hashed password
            $user->password = bcrypt($request->new_password);
            $user->save();
            
            // Log activity
            Helper::createActivity($user, 'Account', 'Update', 'Password has been changed.');

            // ✅ Success response: Password changed successfully
            return response()->json([
                "message"=>"Password changed successfully.",
            ],200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message"=>"Something went really wrong!",
            ],500);            
        }
    }

    /**
     * Delete the currently authenticated user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'max:32',
                function ($attribute, $value, $fail) {
                    // Retrieve the authenticated user
                    $user = auth()->user();

                    // Check if the provided password matches the user's current password
                    if (!Hash::check($value, $user->password)) {
                        $fail("The $attribute you entered is incorrect.");
                    }
                },
            ],
        ]);

         // Retrieve the authenticated user
        $user = auth()->user();

        try {

            // Prevent account deletion if there are negative credits.
            if ($user->credits < 0) {
                return response()->json([
                    "message" => "Cannot delete account due to negative credit! Please resolve outstanding charges!"
                ], 500);
            }

            // Prevent account deletion if the user has active servers.
            if ($user->servers()->exists()) {
                return response()->json([
                    "message" => "Please delete your servers before deleting your account!"
                ], 500);
            }

            // Log activity
            Helper::createActivity($user, 'Account', 'Delete', 'Account has been permanently deleted.');

            // Attempt to delete the user account
            $user->delete();

            // Success response if account deletion is successful
            return response()->json([
                'message' => 'Your account has been deleted.'
            ], 200);

        } catch (\Exception $e) {
            // Error response if an exception occurs during the deletion process
            report($e);
            return response()->json([
                'message' => 'Something went wrong while deleting your account!'
            ], 500);
        }
    }

    /**
     * Logout the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            // Get the authenticated user
            $user = auth()->user();

            // Clear the API token
            $user->tokens()->delete();

            // ✅ Success response
            return response()->json([
                'message' => 'Logged out successfully.'
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response for exceptions
            report($e);
            return response()->json(['message' => 'Failed to log out!'], 500);
        }
    }

    /**
     * Update the confirmation timer for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateConfirmationTimer(Request $request)
    {
        $request->validate([
            'confirmation_timer' => 'required|integer|min:0|max:5',
        ]);

        try {
            $user = auth()->user();

            // Update the user's confirmation timer
            $user->update([
                'confirmation_timer' => $request->confirmation_timer,
            ]);

            Helper::createActivity($user, 'Account', 'Update', 'Confirmation timer updated to ' . $request->confirmation_timer . ' second(s).');

            return response()->json([
                "message" => "Confirmation timer updated successfully."
            ], 200);
        } catch (\Exception $e) {
            report($e->getMessage());
            // ❌ Error response for exceptions
            return response()->json([
                'message' => 'Something went wrong while updating confirmation timer!'
            ], 500);
        }
    }
}