<?php

namespace App\Http;

use App\Helpers\Locale;
use DB;
use Illuminate\Support\Facades\File;
use \Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Admin\{SiteSetting, BillingDetail, Tax};
use App\Models\User;
use App\Models\User\{TwoFaBackupCode};
use App\Models\Admin\Configuration\CloudProvider;
use App\Models\Billing\PromoCode;
use App\Models\Server\Server;
use Cache;
use App\Http\Utilities\Client as CustomClient;
use GuzzleHttp\Client;

class Helper
{	
	// Generate user avatar
	public static function gravatar(string $email, int $size = 200)
	{
		try {
			$gravatarUrl = "https://www.gravatar.com/avatar/".md5( strtolower(trim( $email )))."?d=" . urlencode( "" )."&s={$size}";
		} catch(\Exception $e) {
			$gravatarUrl = "https://www.gravatar.com/avatar/cb8419c1d471d55fbca0d63d1fb2b6ac?d=&s={$size}";
		}
		return $gravatarUrl;
	}

    // Generate a unique token
    public static function generateUniqueToken(int $size = 10, string $table = null, string $column = null)
	{
		$token = Str::random($size);
		if($table && DB::table($table)->where($column,$token)->count()){
			self::generateUniqueToken($size, $table, $column);
		}

		return $token;
	}

    // Check permission
    public static function verifyStoragePermission()
    {
    	try {
	    	// Define the paths to the storage directories
	        $appFolder = storage_path('app');
	        $frameworkFolder = storage_path('framework');
	        $logFolder = storage_path('logs');
	        $cacheFolder = storage_path('framework/cache');

	        // Initialize variables to track folder write and read permissions
	        $isAppFolderWriteable = false;
	        $isFrameworkFolderWriteable = false;
	        $isLogFolderWriteable = false;
	        $isCacheFolderWriteable = false;

	        // Check if the 'app' folder is both writable and readable
	        if (File::isWritable($appFolder) && File::isReadable($appFolder)) {
	            $isAppFolderWriteable = true;
	        }

	        // Check if the 'framework' folder is both writable and readable
	        if (File::isWritable($frameworkFolder) && File::isReadable($frameworkFolder)) {
	            $isFrameworkFolderWriteable = true;
	        }

	        // Check if the 'logs' folder is both writable and readable
	        if (File::isWritable($logFolder) && File::isReadable($logFolder)) {
	            $isLogFolderWriteable = true;
	        }

	        // Check if the 'framework/cache' folder is both writable and readable
	        if (File::isWritable($cacheFolder) && File::isReadable($cacheFolder)) {
	            $isCacheFolderWriteable = true;
	        }

	        // ✅ Success response: Return the permissions for each folder
	        return [
	            'app' => $isAppFolderWriteable,
	            'framework' => $isFrameworkFolderWriteable,
	            'log' => $isLogFolderWriteable,
	            'cache' => $isCacheFolderWriteable,
	        ];
    	} catch(\Exception $e) {
    		return [
	            'app' => false,
	            'framework' => false,
	            'log' => false,
	            'cache' => false,
	        ];
    	}
    }

    public static function siteSetting() {

		try {

			// Retrieve the site setting
	        $siteSetting = SiteSetting::first();
	        if($siteSetting) {
	            // Construct URLs for favicon, logo, and icon images
	            $favicon = null;
	            if($siteSetting->favicon) {
	                $favicon = url('/storage/favicon/'.$siteSetting->favicon);
	            }

	            $logo = null;
	            if($siteSetting->logo){
	                $logo = url('/storage/logo/'.$siteSetting->logo);
	            }

	            $icon = null;
	            if($siteSetting->icon){
	                $icon = url('/storage/icon/'.$siteSetting->icon);
	            }

	            // Update site setting with image URLs

	            $siteSetting->favicon = $favicon;
	            $siteSetting->logo = $logo;
	            $siteSetting->icon = $icon;
	            $siteSetting->redis_password = config('database.redis.default.password');


	            // Check billing exists
	            if (BillingDetail::exists()) {
					// Fetch the necessary fields in one query
					$billingDetail = BillingDetail::select('currency', 'currency_symbol', 'country_code')->first();
					
					// Assign values to site settings
					$siteSetting->currency = $billingDetail->currency;
					$siteSetting->currency_symbol = $billingDetail->currency_symbol;
					$siteSetting->locale = Locale::get($billingDetail->country_code);
				} else {
					// Assign values to site settings
					$siteSetting->currency = "USD";
					$siteSetting->currency_symbol = "$";
					$siteSetting->locale = "en-US";
				}
	        }

	        // ✅ Success response: Return the site setting
            return $siteSetting;

		} catch(\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            return null;
        }
	}

	// Admin Activity
	public static function adminActivity($user, $on, $action, $content)
	{
		$user->adminActivities()->create(['on' => $on, 'action' => $action, 'content' => $content, 'ip' => request()->ip()]);
	}

	// User Activity
	public static function createActivity($user, $on, $action, $content)
	{
		$ip = app()->runningInConsole() ? null : request()->ip();
		$user->activities()->create(['ip' => $ip, 'on' => $on, 'action' => $action, 'content' => $content]);
	}

	// Generate unique google secret
	public static function generateUniqueGoogleSecret($size = 32, string $table = null, string $column = null)
	{
		$google2fa = new \PragmaRX\Google2FAQRCode\Google2FA();
		$key = $google2fa->generateSecretKey($size);

		if($table && DB::table($table)->where($column,$key)->count()) {
			self::generateUniqueGoogleSecret($size, $table, $column);
		}

		return $key;
	}

	// Generate unique backup codes for two factor authentication
	public static function generateUniqueBackupCode()
	{
		$backupCode = Helper::generateUniqueToken(4)."-".Helper::generateUniqueToken(4);

		if(TwoFaBackupCode::where('backup_code', $backupCode)->count()) {
			Helper::generateUniqueBackupCode();
		}

		return $backupCode;
	}

	// Two factor authentication mail send
	public static function twoFactorAuthentication(User $user)
	{
	    if ($user->two_fa_enable && !$user->google2fa_enable) {
	        $ip = request()->ip();
	        $twoFa = $user->twoFa;

	        if (!$twoFa || $twoFa->expires_at < now()) {
	            $token = Helper::generateUniqueToken(6, 'two_fas', 'token');
	            $mailLimit = now()->addMinute();
	            $expiresAt = now()->addMinutes(5);

	            if ($twoFa) {
	                $twoFa->update([
	                    'token' => $token,
	                    'mail_limit' => $mailLimit,
	                    'expires_at' => $expiresAt,
	                    'created_at' => now(),
	                ]);
	            } else {
	                $twoFa = $user->twoFa()->create([
	                    'token' => $token,
	                    'mail_limit' => $mailLimit,
	                    'expires_at' => $expiresAt,
	                ]);
	            }
	        }

	        \Mail::to($user)->queue((new \App\Mail\User\TwoFactorAuthentication($user, $twoFa, $ip, request()->userAgent()))->onQueue('default'));
	    }
	    unset($user->twoFa);

	    return true;
	}

	// To get Location
    public static function getLocation($ip){
        try {
            $client = new Client(['verify' => false]); // Disable SSL verification

            $response = $client->get("https://freeipapi.com/api/json/$ip");
            $contents = $response->getBody()->getContents();

            // Decode the JSON string
            $data = json_decode($contents, true);

            return ["error" => false, "data" => $data];
        } catch (\Exception $e) {
            report($e);
            return ["error" => true, "data" => $e->getMessage()];
        }
    }

    // To Generate unique SSH key name 
    public static function generateUniqueSshKeyName()
	{
		$keyName = config('app.title')."-".Helper::generateUniqueToken(6);

		if(\DB::table('cloud_provider_ssh_keys')->where('name', $keyName)->exists()){
			self::generateUniqueSshKeyName();
		}

		return $keyName;
	}

	// Rename instance name
	public static function renameInstanceName($provider, $name)
	{
		$instanceName = $name."-".self::generateUniqueToken(8);

		if($cloudProvider = CloudProvider::whereProvider($provider)->first()) {
			if($cloudProvider->serverInstances()->whereName($instanceName)->count()) {
				self::renameInstanceName($name, $provider);
			}
		}

		return $instanceName;
	}

	// To check promo code validation & expiration
	public static function checkPromoCodeValidate($user, PromoCode $promoCode)
	{
    	if($promoCode->type == "discount" && $user->email_verified_at == null) {
    		return "The email address ($user->email) needs to be verified before applying this promo code!";
    	} else if ($promoCode->expires_at && $promoCode->expires_at < now()) {
            return "This promo code is expired!";
        } else if ($promoCode->usable && $promoCode->usable <= $promoCode->usage) {
            return "This promo code is expired!";
        } else if (Carbon::parse($user->created_at)->addDays(30) < now() && $promoCode->customer_type == 'new_customer') {
            return "This promo code is available for new customers only!";
        } else if ($user->transactions()->where('promo_code_id', $promoCode->id)->where('status', 1)->count()) {
            return "You have already applied this promo code.";
        } else if($promoCode->type == "discount" && $promoCode->customer_type == 'new_customer' && $user->transactions()->whereNotNull('promo_code_id')->where('status', 1)->count()) {
        	return "This promo code is available for new customers only!";
        } else if($promoCode->customer_type == null) {
        	return "This promo code is expired!";
        }

        return true;
	}

	// Generate unique number
	public static function generateUniqueNumber(int $size = 6, string $table = null, string $column = null)
	{
		$number = mt_rand((int) pow(10, $size-1),(int) pow(10, $size)-1);
		if($table && DB::table($table)->where($column,$number)->count() > 0){
			self::generateUniqueNumber($size, $table, $column);
		}

		return $number;
	}

	// Handle the deletion of the specified server.
    public static function handleServerDeletion(Server $server, $usageSummariesDelete = true)
    {
    	try {
	        // Delete server instance if exists
	        if ($server->serverInstance) {
	            CustomClient::deleteServerInstance($server->serverInstance);
	        }

	        // Delete server from the API
	        if ($server->sa_server_id) {
	            CustomClient::serveravatar("organizations/{$server->sa_org_id}/servers/{$server->sa_server_id}", 'DELETE');
	        }

	        // Delete subscription if exists
	        if ($server->subscription) {
	            $server->subscription->delete();
	        }

	        // Delete usagesummary if exists 
	        if($usageSummariesDelete && $server->usageSummaries()->exists()) {
	            $server->usageSummaries()->delete();
	        }

	        // Delete the server record
	        $server->delete();

    	} catch(\Exception $e) {
    		report($e->getMessage());
    	}
    }

    // Tax calculation based on setting
	public static function calculateTaxAmount($amount, string $countryCode, string $regionCode)
	{
		try{
			// Fetch all tax records
			$taxes = Tax::all()->groupBy('type');

			// Initialize tax variables
			$taxApplied = [];
			$regionTaxSum = 0;
			$countryTaxSum = 0;
			$taxAmount = 0;

			// Calculate the sum of region-specific taxes
			$regionTaxes = $taxes->get('country', []);
			
			foreach ($regionTaxes as $tax) {
				if ($tax->region_code == $regionCode && $tax->country_code == $countryCode) {
					$regionTaxSum += $tax->tax;
					$regionTax = round($amount * ($tax->tax / 100), 3);
					$taxAmount = $taxAmount + $regionTax;
					$taxApplied[] = [
						'type' => $tax->type,
						'country' => $tax->country,
						'country_code' => $tax->country_code,
						'region_code' => $tax->region_code,
						'region' => $tax->region,
						'tax' => $tax->tax,
						'label' => $tax->label,
						'tax_amount' => $regionTax,
					];
				}
			}


			if($regionTaxSum==0){
				foreach ($regionTaxes as $tax) {
					if ($tax->country_code === $countryCode && $tax->region_code === 'all') {
						$countryTaxSum += $tax->tax;
						$countryTax = round($amount * ($tax->tax / 100), 3);
						$taxAmount = $taxAmount + $countryTax;
						$taxApplied[] = [
							'type' => $tax->type,
							'country' => $tax->country,
							'country_code' => $tax->country_code,
							'region_code' => $tax->region_code,
							'region' => $tax->region,
							'tax' => $tax->tax,
							'label' => $tax->label,
							'tax_amount' => $countryTax,
						];
					}
				}
			}

			if ($countryTaxSum==0 && $regionTaxSum==0) {
				$defaultTax = $taxes->get('default', [])[0] ?? null;
				if ($defaultTax) {
					$defaultTaxAmount = round($amount * ($defaultTax->tax / 100), 3);
					$taxAmount = $taxAmount + $defaultTaxAmount;
					$taxApplied[] = [
						'type' => $defaultTax->type,
						'tax' => $defaultTax->tax,
						'label' => $defaultTax->label,
						'country' => $defaultTax->country,
						'country_code'=> $defaultTax->country_code,
						'region'=> $defaultTax->region,
						'region_code'=> $defaultTax->region_code,
						'tax_amount' => $defaultTaxAmount,
					];
				}
			}

			// Return the detailed tax information and calculated amount
			return [
				'tax_applied' => $taxApplied,
				'tax_amount' => round($taxAmount,3),
			];
		} catch(\Exception $e) {
			report($e);
			throw new \Exception($e->getMessage());			
		}
	}

	// Currency detail
	public static function currency()
	{
		try {
			if(BillingDetail::exists()) {
				return [
					"currency" => BillingDetail::value('currency'),
					"currency_symbol" => BillingDetail::value('currency_symbol')
				];
			} else{
				return [
					"currency" => "USD",
					"currency_symbol" => "$"
				];
			}
		} catch(\Exception $e) {
			return [
				"currency" => "USD",
				"currency_symbol" => "$"
			];
		}
	}

	public static function getVultrInstanceTypeName($identifier)
    {
        try {
            $typeNameMap = [
                'all'     => 'All available types',
                'vc2'     => 'Cloud Compute',
                'vdc'     => 'Dedicated Cloud',
                'vhf'     => 'High Frequency Compute',
                'vhp'     => 'High Performance',
                'voc-g'   => 'General Purpose Optimized Cloud',
                'voc-c'   => 'CPU Optimized Cloud',
                'voc-m'   => 'Memory Optimized Cloud',
                'voc-s'   => 'Storage Optimized Cloud',
                'vcg'     => 'Cloud GPU',
                'voc'     => 'All Optimized Cloud types',
            ];

            if (!$identifier) {
                return 'Other';
            }

            foreach ($typeNameMap as $key => $name) {
                if (str_contains($identifier, $key)) {
                    return $name;
                }
            }

            return 'Other';
        } catch (\Exception $e) {
            \Log::info('Vultr type detection failed: ' . $e->getMessage());
            return 'Other';
        }
    }
}