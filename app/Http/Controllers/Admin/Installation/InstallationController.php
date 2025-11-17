<?php

namespace App\Http\Controllers\Admin\Installation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helper;
use App\Models\Admin\BasicDetail;
use App\Enums\UserStatus as UserStatusEnum;
use App\Models\User;
use App\Http\Utilities\Client;

class InstallationController extends Controller
{   
    /**
     * Check and return the storage permissions for each folder.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkPermission(Request $request)
    {
        try {
            
            // ✅ Success response: Return the permissions for each folder
            return response()->json([
                'permission' => Helper::verifyStoragePermission()
            ]);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong!"
            ], 500);
        }
    }

    /**
     * Verify the domain and license key.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'domain' => 'required',
            'license_key' => 'required|max:255'
        ]);

        try {

            // Update or create the "license key" basic detail
            BasicDetail::updateOrCreate(['key' => 'license_key'], ['value' => $request->license_key]);

            // Prepare request data
            $requestData = [
                'domain' => $request->domain,
                'license_key' => $request->license_key
            ];

            // Make an external request to verify domain and license key
            $response = Client::serveravatar("self-hosted/verify", 'post', $requestData);

            // Check if the response contains an error
            if (isset($response['error'])) {
                BasicDetail::where('key', 'license_key')->delete();
                // ❌ Error response
                if (str_contains($response['message'], "Something went wrong")) {
                    return response()->json([
                        'message' => "Invalid license key. Please check and try again!"
                    ], 500);
                } else {
                    // If the string does not contain the substring
                    return response()->json([
                        'message' => $response['message']
                    ], 500);
                }
            }

            \Artisan::call("env:set app_url https://{$request->domain}");

            // ✅ Success response: Domain and license key verified
            return response()->json([
                'message' => $response['message']
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong!"
            ], 500);
        }
    }

    /**
     * Handle user registration.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => ['required', 'string', 'regex:/^[\p{L}\s]+$/u', 'min:3', 'max:255'],
            'email' => 'required|email|unique:users|max:255',
            'country_name' => 'required|string',
            'country_code' => 'required|string',
            'region_name' => 'required|string',
            'region_code' => 'required|string',
            'timezone' => 'required|string',
            'password' => 'required|min:8|max:32|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&._+\-])[A-Za-z\d@$!%*?&._+\-]{8,32}$/',
        ],[
            'password.regex' => 'The password must contain at least one uppercase letter, one digit, and one special character (@$!%*?&_+-.).'
        ]);

        try {
            // Check if an administrator user already exists
            if (User::where('role', 'administrator')->exists()) {
                // ❌ Error response: Only one user is designated as an admin
                return response()->json([
                    "message" => "Only one user is designated as an admin!"
                ], 500);
            }

            // Create a new user with admin privileges
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => "administrator",
                'avatar' => Helper::gravatar($request->email),
                'country_name' => $request->country_name,
                'country_code' => $request->country_code,
                'region_name' => $request->region_name,
                'region_code' => $request->region_code,
                'timezone' => $request->timezone,
                'status' => UserStatusEnum::ACTIVE(),
                'email_verified_at' => now()
            ])->refresh();

            $user['is_admin'] = $user->isSuperAdmin();

            // Generate and save an API token for the new user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Store Login History
            $user->storeLogin();

            // ✅ Success response: Registration successful
            return response()->json([
                'user' => $user,
                'token' => $token,
                'tokenType' => 'Bearer',
                'message' => 'Registration successful.'
            ], 200);

        } catch (\Exception $e) {
            if($user) {
                $user->delete();
            }
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => $e->getMessage() ? $e->getMessage() : "Something went wrong!"
            ], 500);
        }
    }
}
