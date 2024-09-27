<?php

namespace App\Http\Controllers\Admin\Installation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\{SiteSetting, BillingDetail, Smtp};
use App\Models\Admin\Configuration\{Payment, Plan};
use App\Http\Helper;
use App\Http\Utilities\Client;
use Storage;
use Str;
use Http;

class SiteSettingController extends Controller
{
    /**
     * Retrieve and return site settings.
     *
     * @return \Illuminate\Http\Response|null
     */
    public function index()
    {
        try {
            // Return the site setting
            return Helper::siteSetting();
        } catch(\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return null;
        }
    }

    /**
     * Store site setting.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'sa_org_id' => 'required|integer',
            'app_name' => 'required|alpha|max:255',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,ico',
            'icon' => 'nullable|image|mimes:jpeg,jpg,png,ico',
            'favicon' => 'nullable|image|mimes:jpeg,jpg,png,ico',
            'color_code' => ['required', 'max:255', 'regex:/^([a-f0-9]{6}|[a-f0-9]{3})$/i'],
            'tag_line' => 'required|string',
            'analytics' => 'nullable|string|max:255'
        ], [
            'sa_org_id.required' => "The organization field is required." 
        ]);

        try {
            // Check if a SiteSetting exists or create a new one
            if(!$siteSetting = SiteSetting::first()) {
                $siteSetting = new SiteSetting;
            }

            // Update site setting properties
            $siteSetting->sa_org_id = $request->sa_org_id;
            $siteSetting->app_name = $request->app_name;
            $siteSetting->color_code = $request->color_code;
            $siteSetting->tag_line = $request->tag_line;
            $siteSetting->analytics = $request->analytics;

            // Retrieve color palette information from an API
            $colorPalette = Http::get(config('app.color_palette_url').$request->color_code);
            $colorPalette = json_decode($colorPalette);

            if($colorPalette && isset($colorPalette->brand)) {
                $siteSetting->color_palette = $colorPalette->brand;
            }

            // Store logo file
            if(isset($request->logo)) {
                $logo = $request->file('logo');

                if ($logo && $logo->isValid()) {
                    $logoName = Str::uuid().'.'.$logo->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('logo', $logo, $logoName);
                    $siteSetting->logo = $logoName;
                } else {
                    // ❌ Error response: Invalid logo
                    return response()->json([
                        'message' => "Logo is invalid!"
                    ],500);
                }
            }

            // Store only icon file
            if(isset($request->icon)) {
                $icon = $request->file('icon');

                if ($icon && $icon->isValid()) {
                    $smallLogoName = Str::uuid().'.'.$icon->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('icon', $icon, $smallLogoName);
                    $siteSetting->icon = $smallLogoName;
                } else {
                    // ❌ Error response: Invalid icon
                    return response()->json([
                        'message' => "Icon is invalid!"
                    ],500);
                }
            }

            // Store favicon file
            if(isset($request->favicon)) {
                $favicon = $request->file('favicon');

                if ($favicon && $favicon->isValid()) {
                    $faviconName = Str::uuid().'.'.$favicon->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('favicon', $favicon, $faviconName);
                    $siteSetting->favicon = $faviconName;
                } else {
                    // ❌ Error response: Invalid favicon icon
                    return response()->json([
                        'message' => "Favicon icon is invalid!"
                    ],500);
                }
            }

            // Save the site setting
            $siteSetting->save();

            // Update environment variable for app name and redis
            $randomString = Str::random(8);
            \Artisan::call("env:set app_name {$request->app_name}_{$randomString}");
            \Artisan::call("env:set app_title {$request->app_name}");
            \Artisan::call("optimize:clear");
            \Artisan::call("queue:restart");

            Client::serveravatar("self-hosted/setup-completed", 'get');

            // ✅ Success response: Return site setting and a success message
            return response()->json([
                "site_setting" => $siteSetting,
                "message" => "Final setup successfully."
            ],200);

        } catch(\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong!"
            ], 500);
        }
    }
    
    /**
     * Store or update site setting.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'sa_org_id' => 'required|integer',
            'app_name' => 'required|alpha|max:255',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,ico',
            'icon' => 'nullable|image|mimes:jpeg,jpg,png,ico',
            'favicon' => 'nullable|image|mimes:jpeg,jpg,png,ico',
            'color_code' => ['required', 'max:255', 'regex:/^([a-f0-9]{6}|[a-f0-9]{3})$/i'],
            'redis_password' => 'required|string',
            'tag_line' => 'required|string',
            'analytics' => 'nullable|string|max:255',
            'header' => 'nullable|string',
            'footer' => 'nullable|string',
        ],[
            'sa_org_id.required' => "The organization field is required." 
        ]);

        try {
            // Retrieved authenticated user
            $user = auth()->user();

            // Check if a SiteSetting exists or create a new one
            if(!$siteSetting = SiteSetting::first()) {
                $siteSetting = new SiteSetting;
            }

            // Create or Update
            $siteSetting->sa_org_id = $request->sa_org_id;
            $siteSetting->app_name = $request->app_name;
            $siteSetting->tag_line = $request->tag_line;
            $siteSetting->analytics = $request->analytics;
            $siteSetting->header = $request->header;
            $siteSetting->footer = $request->footer;

            if($request->color_code) {
                // Retrieve color palette information from an API
                $colorPalette = Http::get(config('app.color_palette_url').$request->color_code);
                $colorPalette = json_decode($colorPalette);

                if($colorPalette && isset($colorPalette->brand)) {
                    $siteSetting->color_palette = $colorPalette->brand;
                    $siteSetting->color_code = $request->color_code;
                }
            }

            // Delete old logo, icon, and favicon if new files are uploaded
            $this->deleteOldFile("logo/$siteSetting->logo", $request->file('logo'));
            $this->deleteOldFile("icon/$siteSetting->icon", $request->file('icon'));
            $this->deleteOldFile("favicon/$siteSetting->favicon", $request->file('favicon'));

            // Store logo file
            if(isset($request->logo)) {
                $logo = $request->file('logo');

                if ($logo && $logo->isValid()) {
                    $logoName = Str::uuid().'.'.$logo->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('logo', $logo, $logoName);
                    $siteSetting->logo = $logoName;
                } else {
                    // ❌ Error response: Invalid logo
                    return response()->json([
                        'message' => "Logo is invalid!"
                    ],500);
                }
            }

            // Store only icon file
            if(isset($request->icon)) {
                $icon = $request->file('icon');

                if ($icon && $icon->isValid()) {
                    $smallLogoName = Str::uuid().'.'.$icon->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('icon', $icon, $smallLogoName);
                    $siteSetting->icon = $smallLogoName;
                } else {
                    // ❌ Error response: Invalid icon
                    return response()->json([
                        'message' => "Icon is invalid!"
                    ],500);
                }
            }

            // Store favicon file
            if(isset($request->favicon)) {
                $favicon = $request->file('favicon');

                if ($favicon && $favicon->isValid()) {
                    $faviconName = Str::uuid().'.'.$favicon->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('favicon', $favicon, $faviconName);
                    $siteSetting->favicon = $faviconName;
                } else {
                    // ❌ Error response: Invalid favicon icon
                    return response()->json([
                        'message' => "Favicon icon is invalid!"
                    ],500);
                }
            }

            // Save the site setting
            $siteSetting->save();
            
            \Artisan::call("env:set app_title {$request->app_name}");
            if($request->redis_password) {
                \Artisan::call("env:set redis_password {$request->redis_password}");
                \Artisan::call("env:set cache_driver redis");
                \Artisan::call("env:set queue_connection redis");
            }
            \Artisan::call("queue:restart");
            
            // Store Admin Activity
            Helper::adminActivity($user, 'Whitelabel', 'Update', 'Site settings have been updated.');

            // ✅ Success response
            return response()->json([
                "message" => "Site setting has been updated successfully."
            ],200);

        } catch (\Exception $e) {
            // ❌ Error response
            report($e);
            return response()->json([
                'message' => "Something want really wrong while getting details!",
            ],500);
        }
    }

    /**
     * Delete the old file if it exists.
     *
     * @param  string|null  $oldPath The path of the old file to be deleted.
     * @param  mixed  $newFile The new file to replace the old one.
     * @return void
     */
    private function deleteOldFile($oldPath, $newFile)
    {
        // Check if there is a new file to replace the old one, and if the old path exists in storage
        if ($newFile && $oldPath && Storage::disk('public')->exists($oldPath)) {
            // If both conditions are met, delete the old file
            Storage::disk('public')->delete($oldPath);
        }
    }

    /**
     * Store or update billing detail.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function billingDetailStore(Request $request)
    {
        // Create Incoming request array
        $validationArray = [
            'name' => ['required', 'string', 'regex:/^[\p{L}\s]+$/u', 'min:3', 'max:255'],
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'city' => ['required', 'string', 'regex:/^[\p{L}\s]+$/u', 'max:255'],
            'postal_code' => 'required|regex:/^[A-Z0-9\s\-]+$/i|min:3|max:15',
            'country' => 'required|string',
            'country_code' => 'required|string',
            'region' => 'required|string',
            'region_code' => 'required|string',
            'billing_mode' => 'required|in:prepaid,postpaid',
            'new_registration_free_credits' => 'required|numeric|max:2000',
            'minimum_recharge_amount' => 'required|numeric|max:2000',
            'due_days' => 'required|integer|max:30',
            'retention_period' => 'required|integer|max:30',
            'currency' => 'required|string|max:255',
            'currency_symbol' => 'required|string|max:255',
            'tax_numbers' => [
                "nullable",
                "array",
                function ($attribute, $value, $fail) {
                    foreach ($value as $taxVar) {
                         if (!isset($taxVar['name']) || !isset($taxVar['value'])) {
                            $fail("Each entry requires both a variable name and value.");
                            return;
                        }
                    }
                },
            ],
        ];

        // Define custom error messages
        $messages = [
            "new_registration_free_credits.required" => "The free signup credit field is required.",
            "new_registration_free_credits.numeric" => "The free signup credit field must be a number.",
            "new_registration_free_credits.max" => "The free signup credit field must not be greater than 2000.",
            "minimum_recharge_amount.required" => "The minimum recharge field is required.",
            "minimum_recharge_amount.numeric" => "The minimum recharge field must be a number.",
            "minimum_recharge_amount.max" => "The minimum recharge field must not be greater than 2000.",
            "due_days.required" => "The payment due field is required.",
            "due_days.integer" => "The payment due field must be an integer.",
            "due_days.max" => "The payment due field must not be greater than 30.",
            "retention_period.required" => "The retention time field is required.",
            "retention_period.integer" => "The retention time field must be an integer.",
            "retention_period.max" => "The retention time field must not be greater than 30."
        ];

        // Validate the incoming request
        $request->validate($validationArray, $messages);

        try {
            // Retrieved authenticated user
            $user = auth()->user();

            // Check if a BillingDetail exists or create a new one
            if(!$billingDetail = BillingDetail::first()) {
                $billingDetail = new BillingDetail;
                $billingDetail->currency = $request->currency;
                $billingDetail->currency_symbol = $request->currency_symbol;
            }

            // Create or Update
            $billingDetail->name = $request->name;
            $billingDetail->email = $request->email;
            $billingDetail->address = $request->address;
            $billingDetail->city = $request->city;
            $billingDetail->postal_code = $request->postal_code;
            $billingDetail->country = $request->country;
            $billingDetail->country_code = $request->country_code;
            $billingDetail->region = $request->region;
            $billingDetail->region_code = $request->region_code;
            $billingDetail->billing_mode = $request->billing_mode;
            $billingDetail->tax_numbers = $request->tax_numbers;
            $billingDetail->new_registration_free_credits = $request->new_registration_free_credits;
            $billingDetail->minimum_recharge_amount = $request->minimum_recharge_amount;
            $billingDetail->due_days = $request->due_days;
            $billingDetail->retention_period = $request->retention_period;

            // Save the Billing Details
            $billingDetail->save();

            // Store Activity
            Helper::adminActivity($user, 'Billing', 'Update', 'Billing details have been updated.');

            // ✅ Success response: Return Billing Detail and a success message
            return response()->json([
                "message" => "Billing Detail has been stored successfully.",
                "billingDetail" => $billingDetail,
            ],200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went wrong!",
            ],500);
        }
    }

    /**
     * Retrieve and return billing detail.
     *
     * @return \Illuminate\Http\Response
     */
    public function BillingIndex()
    {
        try {
            // Retrieve the first billing detail record from the database
            $billingDetail = BillingDetail::first();
            
            // ✅ Success response: Return Billing Detail
            return response()->json([
                "billingDetail" => $billingDetail,
            ],200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went wrong!",
            ],500);
        }
    }

    /**
     * Fetch and return a list of organizations from an external API.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrganization(){
        try {
            // Make a GET request to the external API to retrieve organizations
            $response = Client::serveravatar("self-hosted/organizations", 'GET');

            // Check if an error occurred in the response
            if (isset($response['error'])) {
                // Return null for servers if error generated
                return response()->json([
                    'message' => $response['message'],
                ], 500);
            }

            // Initialize empty array
            $organizations = [];

            // Map the organizations from the response
            $organizations = array_map(function ($organization) {
                return [
                    'id' => $organization['id'],
                    'name' => $organization['name'],
                ];
            }, $response['organizations']);

            // ✅ Success response: Return organizations
            return response()->json([
                "organizations" => $organizations,
            ],200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Check and return the setup status of various components.
     *
     * @return \Illuminate\Http\Response
     */
    public function setup(){
        try {
            // Check the existence of different setup components
            $setup = [
                "application_setup" => SiteSetting::exists(),
                "billing_management" => BillingDetail::exists(),
                "smtp_setup" => Smtp::exists(),
                "payment_integration" => Payment::exists(),
                "plan_management" => Plan::exists(),
            ];

            // ✅ Success response: Return setup components
            return response()->json([
                "setup" => $setup,
            ],200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!"
            ],500);
        }
    }
}