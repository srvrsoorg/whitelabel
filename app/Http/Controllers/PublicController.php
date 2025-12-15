<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;
use App\Http\Helper;
use App\Models\Admin\{Smtp, Database, SiteSetting, BasicDetail, BillingDetail};
use App\Models\Admin\Configuration\Payment;
use App\Enums\PaymentGateway as PaymentGatewayEnum;
use App\Enums\UserStatus as UserStatusEnum;
use App\Models\User;
use App\Models\User\UserMeta;
use App\Mail\User\EmailVerification;
use Illuminate\Support\Facades\File;
use DateTimeZone;
use DateTime;
use Mail;
use Carbon\Carbon;

class PublicController extends Controller
{

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => ['required', 'string', 'regex:/^[\p{L}\s]+$/u', 'min:3', 'max:255'],
            'email' => 'required|email|max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'country_name' => 'required|string',
            'country_code' => 'required|string',
            'region_name' => 'required|string',
            'region_code' => 'required|string',
            'password' => 'required|min:8|max:32|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&._+\-])[A-Za-z\d@$!%*?&._+\-]{8,32}$/'
        ],[
            'password.regex' => 'The password must contain at least one uppercase letter, one digit, and one special character (@$!%*?&_+-.).'
        ]);

        try {
            // fetch new registration free credits from admin's billing details
            $freeCredits = BillingDetail::value('new_registration_free_credits') ?? 0;

            // Create a new user record in the database
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'avatar' => Helper::gravatar($request->email),
                'status' => UserStatusEnum::PENDING(),
                'country_name' => $request->country_name,
                'country_code' => $request->country_code,
                'credits' => $freeCredits,
                'region_name' => $request->region_name,
                'region_code' => $request->region_code,
            ])->refresh();

            // Create a meta with verification token
            $verificationToken = $user->metas()->updateOrCreate([
                'name' => 'verification_token'
            ],[
                'value' => Helper::generateUniqueToken(32,'user_metas','value')
            ]);
            if(Smtp::exists()) {
                // Send user an E-mail verification link
                Mail::to($user)->queue((new EmailVerification($user, $verificationToken->value))->onQueue('default'));
            }

            // Add 'is_admin' field to the user object
            $user['is_admin'] = $user->isSuperAdmin();

            // Create an authentication token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Store Login History
            $user->storeLogin();

            // ✅ Success response: User registered successfully
            return response()->json([
                'user' => $user,
                'token' => $token,
                'tokenType' => 'Bearer',
                'message' => 'You have successfully registered.'
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Handle user login.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        // Request validation
        $request->validate([
            'email' => 'required|email|max:255|exists:users,email,deleted_at,NULL',
            'password' => 'required|min:8|max:32'
        ],[
            'email.exists' => 'The selected email is not registered.',
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            if (in_array($user->status, [UserStatusEnum::BANNED(), UserStatusEnum::LOCKED()])) {
                return response()->json([
                    "message" => "Sorry! you are temporary $user->status!",
                ],500);
            }

            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 

                $user = auth()->user();
                $user['is_admin'] = $user->isSuperAdmin();

                if($user->two_fa_enable) {
                    Helper::twoFactorAuthentication($user);
                    $token = null;
                    $tokenType = null;
                } else {
                    $user->storeLogin();
                    $token = $user->createToken('auth_token')->plainTextToken;
                    $tokenType = "Bearer";
                }

                return response()->json([
                    'user' => $user,
                    'token' => $token,
                    'tokenType' => $tokenType,
                    'message' => 'You have successfully logged in.'
                ], 200);
            } else { 
                return response()->json(['message'=>'Invalid username or password!'], 500);
            }             
        } catch(\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Retrieve a list of timezones with their offsets from UTC.
     *
     * @return array List of timezones with their offsets from UTC.
     */
    public function timezone()
    {
        try {
            $regions = array(
                DateTimeZone::AFRICA,
                DateTimeZone::AMERICA,
                DateTimeZone::ANTARCTICA,
                DateTimeZone::ASIA,
                DateTimeZone::ATLANTIC,
                DateTimeZone::AUSTRALIA,
                DateTimeZone::EUROPE,
                DateTimeZone::INDIAN,
                DateTimeZone::PACIFIC,
            );

            $timezones = array();
            foreach( $regions as $region ) {
                $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
            }

            $timezone_offsets = array();
            foreach( $timezones as $timezone ) {
                $tz = new DateTimeZone($timezone);
                $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
            }

            $etcTz = new DateTimeZone("Etc/UTC");
            $timezone_offsets["Etc/UTC"] = $etcTz->getOffset(new DateTime);

            // Sort timezones by offset
            asort($timezone_offsets);
            $timezone_list = array();

            foreach( $timezone_offsets as $timezone => $offset)
            {
                $offset_prefix = $offset < 0 ? '-' : '+';
                $offset_formatted = gmdate('H:i', abs($offset));

                $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

                $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
            }

            return $timezone_list;
        } catch (\Exception $e) {
            report($e);
            // Error response
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Send a password reset link to the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        // Request Validations
        $request->validate([
            'email' => 'required|email|max:255|exists:users,email,deleted_at,NULL',
        ],[
            'email.exists' => 'The provided email is not registered.',
        ]);

        try {
            if (!Smtp::exists()) {
                return response()->json([
                    "message" => "Smtp configuration not found!"
                ], 500);
            }

            // Retrieve user by email
            $user = User::where('email', $request->email)->first();

            // Retrieve existing password reset record
            $passwordReset = DB::table('password_resets')->where('email', $user->email)->first();

            if ($passwordReset) {
                if (Carbon::parse($passwordReset->created_at)->addMinutes(2) > now()) {
                    return response()->json([
                        "message" => "Please wait 2 minutes before requesting a new password reset!",
                    ], 500);
                }
            }

            // Generate a new unique token
            $token = Helper::generateUniqueToken(32, 'password_resets', 'token');

            // Insert or update the token in the password resets table
            DB::table('password_resets')->updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => now()]
            );
                
            // Send a password reset link to the user via email
            \Mail::to($user->email)->send(new \App\Mail\User\PasswordResetLink($user, $token));

            // Success response
            return response()->json([
                'message' => "You will shortly receive a password reset link."
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Reset the user's password using a password reset token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
        ]);

        if (!$token = DB::table('password_resets')->where('token', $request->token)->first()) {
            // Not found response
            return response()->json([
                'message' => "Password reset token is invalid!"
            ], 404);
        }

        $user = User::where('email', $token->email)->first();
      
        // Request Validations
        $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:32',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&._+\-])[A-Za-z\d@$!%*?&._+\-]{8,32}$/',
                function($attribute, $value, $fail) use ($user){
                    if(Hash::check($value, $user->password)){
                        $fail("New password must be different from your old password.");
                    }
                }
            ]
        ],[
            'password.regex' => 'The password must contain at least one uppercase letter, one digit, and one special character (@$!%*?&_+-.).'
        ]);

        try {
            $user->password = bcrypt($request->password);
            $user->save();

            // Revoke all tokens
            foreach ($user->tokens as $token) {
                $token->delete();
            }

            // Delete the token
            DB::table('password_resets')->where('token', $request->token)->delete();

            // Success response
            return response()->json([
                'message' => "Your password has been updated successfully."
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ], 500);
        }
    }

    /**
     * Verify user's email address using the provided token.
     *
     * @param  string  $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify($token)
    {
        try {

            // find verification token
           if($verificationToken = UserMeta::where('name', 'verification_token')->where('value', $token)->first()){
                // Update user's email verification timestamp and status
                $user = $verificationToken->user;
                $user->email_verified_at = now();
                $user->status = UserStatusEnum::ACTIVE();
                $user->save();

                // Create activity
                Helper::createActivity($user, 'Account', 'Verify', 'Verified account using verification link.');

                // Delete token
                $verificationToken->delete();

                // Success response
                return response()->json([
                    'message' => "E-mail address has been verified successfully."
                ],200);
            } else {
                // Not found response
                return response()->json([
                    'message' => "This E-mail verification link is invalid! Please contact support for help!"
                ],404);
            }
        } catch (\Exception $e){
            // Error response
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Resend the email verification link to the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerificationLink(){
        try {
            // Retrieve authenticated user
            $user = auth()->user();

            // Check that email has been already verified 
            if($user->email_verified_at){
                return response()->json([
                    "message" => "Your email is already verified!",
                ],500);
            }

            // Create a meta with verification token
            $verificationToken = $user->metas()->updateOrCreate([
                'name' => 'verification_token'
            ],[
                'value' => Helper::generateUniqueToken(32,'user_metas','value')
            ]);
            
            // Send user an E-mail verification link
            \Mail::to($user)->queue((new EmailVerification($user, $verificationToken->value))->onQueue('default'));

            // Success response
            return response()->json([
                "message" => "Verififcation link has been resend to your registered email."
            ],200);
        } catch (\Exception $e) {
            // Error response
            report($e);
            return response()->json([
                "message" => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Retrieve the installation steps status.
     *
     * @return array
     */
    public function installationSteps() {
        try {
            $setup['database'] = Database::exists();
            $setup['smtp'] = Smtp::exists();
            $setup['site_setting'] = SiteSetting::exists();
            $setup['license_key'] = BasicDetail::where('key', 'license_key')->exists();
            $setup['register'] = User::exists();

            //Check storage permission
            $setup['storage_permission'] = $this->checkStoragePermisison();

            return $setup;

        } catch (\Exception $e) {
            report($e);
            $setup['database'] = false;
            $setup['smtp'] = false;
            $setup['site_setting'] = false;
            $setup['license_key'] = false;
            $setup['register'] = false;
            
            //Check storage permission
            $setup['storage_permission'] = $this->checkStoragePermisison();

            return $setup;
        }

    }

    // check storage permission
    public function checkStoragePermisison()
    {
        //Check storage permission
        $permission = Helper::verifyStoragePermission();

        $result = array_reduce(array_values($permission), function ($carry, $value) {
            return $carry && $value;
        }, true);

        return $result;
    }

    // Enable Providers
    public function enableProviders()
    {
        try {
            $providers = [
                'payment' => json_decode(Payment::where('enabled', true)->pluck('provider')),
                'basic_details' => BillingDetail::select('minimum_recharge_amount', 'currency', 'currency_symbol')->first(),
                'razorpay_client_id' => Payment::where('provider',PaymentGatewayEnum::RAZORPAY())->value('client_id')
            ];

            return $providers;
        } catch(\Exception $e) {
            report($e);
        }
    }

    /**
     * Retrieve the countries.
     *
     * @return array
     */
    public function countries()
    {
        try{
            $countriesWithStates = \DB::table('countries')
                ->select('countries.name as country_name', 'countries.iso2 as country_iso2', 'states.name as state_name', 'states.iso2 as state_iso2')
                ->join('states', 'countries.iso2', '=', 'states.country_code')
                ->orderBy('countries.name')
                ->orderBy('states.name')
                ->get()
                ->groupBy('country_name') // Group by country name
                ->map(function ($country) {
                    return [
                        'country_name' => $country->first()->country_name,
                        'iso2' => $country->first()->country_iso2,
                        'states' => $country->filter(function ($state) {
                            return !is_null($state->state_name); // Filter out null states
                        })->map(function ($state) {
                            return [
                                'state_name' => $state->state_name,
                                'state_iso2' => $state->state_iso2
                            ];
                        })->values()->all()
                    ];
                })->values()->all();


            return response()->json([
                'countries' => $countriesWithStates
            ]);
        }catch(\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'Something went wrong!'
            ], 500);
        }
    }

    /**
     * Retrieve the currencies.
     *
     * @return array
     */
    public function currencies()
    {
        try {
            // Path to the JSON file
            $path = base_path('config/currencies.json');

            // Check if the file exists
            if (!File::exists($path)) {
                return response()->json([
                    'message' => 'Currency file not found!'
                ], 404);
            }

            // Read the file content
            $json = File::get($path);

            // Decode JSON into an associative array
            $currencies = json_decode($json, true);

            // Return a JSON response with the data in the expected format
            return response()->json([
                'currencies' => $currencies
            ]);
        } catch (\Exception $e) {
            // Log the exception
            report($e);

            // Return a JSON response with an error message
            return response()->json([
                'message' => $e->getMessage() ? $e->getMessage() : 'Something went wrong!'
            ], 500);
        }
    }
}