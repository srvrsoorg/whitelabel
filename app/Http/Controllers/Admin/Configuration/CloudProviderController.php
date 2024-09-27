<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Configuration\{CloudProvider, Plan};
use App\Enums\CloudProvider as CloudProviderEnums;
use Aws\Exception\AwsException;
use Exception;
use App\Http\Helper;
use phpseclib\Crypt\RSA;
use App\Http\Utilities\Client;
use Storage;

class CloudProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $cloudProviders = CloudProvider::withCount('plans')
                ->withCount(['plans as active_plans' => function ($query) {
                    $query->where('visible', true);
                }])->get();

            return response()->json([
                "cloudProvider" => $cloudProviders,
            ],200);
        } catch (Exception $e) {            
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Define allowed values
        $validProviders = ['lightsail', 'vultr', 'linode', 'hetzner', 'digitalocean'];
        $validActions = ['create', 'update'];

        // Get request parameters
        $provider = request()->get('provider');
        $action = request()->get('action');

        // Validate provider and action
        if (!in_array($provider, $validProviders)) {
            return response()->json(["message" => "Invalid provider!"], 500);
        }

        if (!in_array($action, $validActions)) {
            return response()->json(["message" => "Invalid action!"], 500);
        }

        // Define base validation rules
        $baseRules = [
            "provider" => ['required', 'string', 'in:' . implode(',', $validProviders)],
            "access_key" => "required|string",
            "access_secret" => [
                'nullable',
                'required_if:provider,lightsail',
                'string'
            ],
        ];

        if ($action === 'create') {
            // Add unique validation for create action
            $rules = array_merge($baseRules, [
                "provider" => array_merge($baseRules['provider'], ['unique:cloud_providers,provider,NULL,id,deleted_at,NULL']),
            ]);
        } elseif ($action === 'update') {
            // Add unique validation for update action
            $cloudProvider = CloudProvider::where('provider', $provider)->first();
            if (!$cloudProvider) {
                return response()->json(["message" => "Cloud platform not found!"], 404);
            }
            $rules = array_merge($baseRules, [
                "provider" => array_merge($baseRules['provider'], ['unique:cloud_providers,provider,' . $cloudProvider->id . ',id,deleted_at,NULL']),
            ]);
        }

        // Define custom error messages
        $messages = [
            "access_key.required_if" => "The access key field is required when the provider is lightsail.",
            "access_secret.required" => "The access secret field is required.",
        ];

        // Validate request
        $request->validate($rules, $messages);

        try {
            $provider = $request->provider;
            $user = auth()->user();

            if ($provider == CloudProviderEnums::LIGHTSAIL()) {
                return $this->handleLightsailStore($request, $user);
            } elseif ($provider == CloudProviderEnums::LINODE()) {
                return $this->handleLinodeStore($request, $user);
            } elseif ($provider == CloudProviderEnums::HETZNER()) {
                return $this->handleHetznerStore($request, $user);
            } elseif ($provider == CloudProviderEnums::VULTR()) {
                return $this->handleVultrStore($request, $user);
            } elseif ($provider == CloudProviderEnums::DIGITALOCEAN()) {
                return $this->handleDigitalOceanStore($request, $user);
            } else {
                // ❌ Not Found
                return response()->json([
                    'message' => "Server provider not found!"
                ], 404);
            }

        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);

            if (method_exists($e, 'getResponse') && $response = $e->getResponse()) {
                $exception = json_decode((string) $response->getBody());
                if(isset($exception->message)) {
                    //lightsail error
                    return response()->json([
                        'message' => $exception->message
                    ],500);
                } else if(isset($exception->error->message)) {
                    //hetzner error
                    return response()->json([
                        'message' => $exception->error->message
                    ],500);
                } else if(isset($exception->error)) {
                    //vultr error
                    return response()->json([
                        'message' => $exception->error
                    ],500);
                } else {
                    return response()->json([
                        'message' => $exception
                    ],500);
                }
            }
        }
    }

    /**
     * Handle the storage of credentials for Lightsail cloud provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\Response
     */
    protected function handleLightsailStore(Request $request, $user)
    {
        if($request->provider == CloudProviderEnums::LIGHTSAIL()) {
            $provider = "Amazon Lightsail";
            try {
                $lightsailClient = Client::lightsail($request->access_key, $request->access_secret, "ap-south-1");
                    
                $bundle = $lightsailClient->getBundles(['includeInactive' => false]);
            }  catch(AwsException $aws) {
                report($aws);
                // ❌ Error response
                return response()->json([
                    'message' => $aws->getAwsErrorMessage()
                ],500);
            }
        }

        try {
            $providerAndKey = $this->generateKey($request);
            $cloudProvider = $providerAndKey['cloudProvider'];

            //ssh key generate
            $serverKeyName = Helper::generateUniqueToken(32, "cloud_provider_ssh_keys", "key");
            $keyName = Helper::generateUniqueSshKeyName();
            $sshKey = $cloudProvider->key;
            $providerSshKey = $cloudProvider->sshKeys()->where('region', 'ap-south-1')->first();

            if($providerSshKey == null) {
                
                $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $keyName, 'ap-south-1');
                if(isset($sshKeyData['error'])) {
                    // ❌ Error response
                    return response()->json([
                        'message' => $sshKeyData['message']
                    ],500);
                }

                // Create ssh key detail
                $providerSshKey = $cloudProvider->sshKeys()->updateOrCreate([
                    "provider_id" => $cloudProvider->id,
                    "region" => 'ap-south-1',
                ],[
                    "name" => $keyName,
                    "ssh_key_id" => $sshKeyData['operation']['id'],
                    "key" => $serverKeyName
                ]);
            } else {
                // If provider SSH key exists, retrieve it
                try {
                    // Get the Lightsail client
                    $client = Client::lightsail($cloudProvider->access_key, $cloudProvider->access_secret, 'ap-south-1');

                    $sshKeyData = $client->getKeyPair(["keyPairName" => $providerSshKey->name]);
                } catch (AwsException $aws) {
                    // If retrieval fails, recreate the SSH key
                    $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $providerSshKey->name, 'ap-south-1');
                    if (isset($sshKeyData['error'])) {
                        // ❌ Error response
                        return response()->json([
                            'message' => $sshKeyData['message']
                        ],500);
                    }

                    $providerSshKey->ssh_key_id = $sshKeyData['operation']['id'];
                    $providerSshKey->save();
                }
            }

            return $this->getResponse($user, $provider);

        } catch (AwsException $exception) {
            report($exception);
            // ❌ Error response
            return response()->json([
                'message' => $exception->getAwsErrorMessage()
            ],500);
        }
    }

    /**
     * Handle the storage of credentials for Digitalocean cloud provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\Response
     */
    protected function handleDigitalOceanStore(Request $request, $user)
    {
        try {
            $provider = "Digitalocean";
            $accountInfo = Client::digitalOcean("actions", "GET", $request->access_key);

            if (isset($accountInfo['error'])) {
                // ❌ Error response
                return response()->json([
                    'message' => $accountInfo['message']
                ], 500);
            }

            // Generate SSH key
            $providerAndKey = $this->generateKey($request);

            $cloudProvider = $providerAndKey['cloudProvider'];

            //ssh key generate
            $serverKeyName = Helper::generateUniqueToken(32, "cloud_provider_ssh_keys", "key");
            $keyName = Helper::generateUniqueSshKeyName();
            $sshKey = $cloudProvider->key;

            // SSH key creation logic
            $providerSshKey = $cloudProvider->sshKeys()->first();

            if ($providerSshKey == null) {
                // ✅ store ssh key in provider account
                $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $keyName);
                if(isset($sshKeyData['error'])) {
                    // ❌ Error response
                    return response()->json([
                        'message' => $sshKeyData['message']
                    ],500);
                }

                $sshKeyData = json_decode($sshKeyData);

                // Create ssh key detail
                $providerSshKey = $cloudProvider->sshKeys()->updateOrCreate([
                    "provider_id" => $cloudProvider->id
                ], [
                    "name" => $keyName,
                    "ssh_key_id" => $sshKeyData->ssh_key->id,
                    "key" => $serverKeyName
                ]);
            } else {
                // If provider SSH key exists, check if it exists in the provider account
                $sshKeyData = Client::digitalOcean("account/keys/$providerSshKey->ssh_key_id", "GET", $cloudProvider->access_key);

                // If SSH key doesn't exist in the provider account, add it
                if (isset($sshKeyData['error'])) {
                    $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $providerSshKey->name);
                    if (isset($sshKeyData['error'])) {
                        // ❌ Error response
                        return response()->json([
                            'message' => $sshKeyData['message']
                        ],500);
                    }
                    $sshKeyData = json_decode($sshKeyData);
                    $providerSshKey->ssh_key_id = $sshKeyData->ssh_key->id;
                    $providerSshKey->save();
                }
            }
            return $this->getResponse($user, $provider);

        } catch (\Exception $e) {
            // Handle exceptions
            report($e);
            if ($e->getMessage()) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 500);
            }
            // Handle other exceptions
            return response()->json([
                'message' => "Something went wrong! Please check all the details again."
            ], 500);
        }
    }

    /**
     * Handle the storage of credentials for Linode cloud provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\Response
     */
    protected function handleLinodeStore(Request $request, $user)
    {
        try {
            $provider = "Linode";
            $tagList = Client::linode("tags", "GET", $request->access_key);
            
            if (isset($tagList['error'])) {
                // ❌ Error response
                return response()->json([
                    'message' => $tagList['message']
                ], 500);
            }
            
            $tagList = json_decode($tagList);
            $data = $tagList->data;
            $tag = config('app.title');

            if (collect($data)->where('label', $tag)->isNotEmpty()) {
                Client::linode("tags/$tag", "DELETE", $request->access_key);
            }

            $accountInfo = Client::linode("tags", "POST", $request->access_key, json_encode(['label' => $tag]));

            if (isset($accountInfo['error'])) {
                // ❌ Error response
                return response()->json([
                    'message' => $accountInfo['message']
                ], 500);
            }

            Client::linode("tags/$tag", "DELETE", $request->access_key);

            // Generate SSH key
            $providerAndKey = $this->generateKey($request);

            $cloudProvider = $providerAndKey['cloudProvider'];

            //ssh key generate
            $serverKeyName = Helper::generateUniqueToken(32, "cloud_provider_ssh_keys", "key");
            $keyName = Helper::generateUniqueSshKeyName();
            $sshKey = $cloudProvider->key;

            // SSH key creation logic
            $providerSshKey = $cloudProvider->sshKeys()->first();

            if ($providerSshKey == null) {
                // ✅ store ssh key in provider account
                $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $keyName);
                if(isset($sshKeyData['error'])) {
                    // ❌ Error response
                    return response()->json([
                        'message' => $sshKeyData['message']
                    ],500);
                }

                $sshKeyData = json_decode($sshKeyData);

                // Create ssh key detail
                $providerSshKey = $cloudProvider->sshKeys()->updateOrCreate([
                    "provider_id" => $cloudProvider->id
                ], [
                    "name" => $keyName,
                    "ssh_key_id" => $sshKeyData->id,
                    "key" => $serverKeyName
                ]);
            } else {
                $sshKeyData = Client::linode("profile/sshkeys/$providerSshKey->ssh_key_id", "GET", $cloudProvider->access_key);

                if (isset($sshKeyData['error'])) {
                    $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $providerSshKey->name);
                    if (isset($sshKeyData['error'])) {
                        // ❌ Error response
                        return response()->json([
                            'message' => $sshKeyData['message']
                        ],500);
                    }

                    $sshKeyData = json_decode($sshKeyData);
                    $providerSshKey->ssh_key_id = $sshKeyData->id;
                    $providerSshKey->save();
                }
            }

            return $this->getResponse($user, $provider);

        } catch (\Exception $e) {
            // Handle exceptions
            report($e);
            if ($e->getMessage()) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 500);
            }
        }
    }

    /**
     * Handle the storage of credentials for Hetzner cloud provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\Response
     */
    protected function handleHetznerStore(Request $request, $user)
    {
        try {
            $provider = "Hetzner";
            $accountInfo = Client::hetzner("locations", "GET", $request->access_key);

            if (isset($accountInfo['error'])) {
                // ❌ Error response
                return response()->json([
                    'message' => $accountInfo['message']
                ], 500);
            }

            // Generate SSH key
            $providerAndKey = $this->generateKey($request);

            $cloudProvider = $providerAndKey['cloudProvider'];

            //ssh key generate
            $serverKeyName = Helper::generateUniqueToken(32, "cloud_provider_ssh_keys", "key");
            $keyName = Helper::generateUniqueSshKeyName();
            $sshKey = $cloudProvider->key;

            // SSH key creation logic
            $providerSshKey = $cloudProvider->sshKeys()->first();

            if ($providerSshKey == null) {
                // ✅ store ssh key in provider account
                $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $keyName);
                if(isset($sshKeyData['error'])) {
                    // ❌ Error response
                    return response()->json([
                        'message' => $sshKeyData['message']
                    ],500);
                }

                $sshKeyData = json_decode($sshKeyData);

                // Create ssh key detail
                $providerSshKey = $cloudProvider->sshKeys()->updateOrCreate([
                    "provider_id" => $cloudProvider->id
                ], [
                    "name" => $keyName,
                    "ssh_key_id" => $sshKeyData->ssh_key->id,
                    "key" => $serverKeyName
                ]);
            } else {
                $sshKeyData = Client::hetzner("ssh_keys/$providerSshKey->ssh_key_id", "GET", $cloudProvider->access_key);

                if (isset($sshKeyData['error'])) {
                    // Store SSH key in provider account
                    $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $providerSshKey->name);
                    if (isset($sshKeyData['error'])) {
                        // ❌ Error response
                        return response()->json([
                            'message' => $sshKeyData['message']
                        ],500);
                    }

                    $sshKeyData = json_decode($sshKeyData);
                    $providerSshKey->ssh_key_id = $sshKeyData->ssh_key->id;
                    $providerSshKey->save();
                }
            }

            return $this->getResponse($user, $provider);

        } catch (\Exception $e) {
            // Handle exceptions
            report($e);
            if ($e->getMessage()) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 500);
            }
        }
    }

    /**
     * Handle the storage of credentials for Vultr cloud provider.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\Response
    */
    public function handleVultrStore(Request $request, $user)
    {
        $provider = "Vultr";
        try {
            $accountInfo = Client::vultr("account", "GET", $request->access_key);

            if(isset($accountInfo['error'])) {
                // ❌ Error response
                return response()->json([
                    'message' => $accountInfo['message']
                ],500);
            }

            // Generate SSH key
            $providerAndKey = $this->generateKey($request);

            $cloudProvider = $providerAndKey['cloudProvider'];

            //ssh key generate
            $serverKeyName = Helper::generateUniqueToken(32, "cloud_provider_ssh_keys", "key");
            $keyName = Helper::generateUniqueSshKeyName();
            $sshKey = $cloudProvider->key;

            $providerSshKey = $cloudProvider->sshKeys()->first();

            if($providerSshKey == null) {
                // ✅ store ssh key in provider account
                $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $keyName);

                if(isset($sshKeyData['error'])) {
                    // ❌ Error response
                    return response()->json([
                        'message' => $sshKeyData['message']
                    ],500);
                }  

                $sshKeyData = json_decode($sshKeyData);

                // Create ssh key detail
                $providerSshKey = $cloudProvider->sshKeys()->updateOrCreate([
                    "provider_id" => $cloudProvider->id],[
                    "name" => $keyName,
                    "ssh_key_id" => $sshKeyData->ssh_key->id,
                    "key" => $serverKeyName
                ]);
            } else {
                // Check SSH key in provider account
                $sshKeyData = Client::vultr("ssh-keys/$providerSshKey->ssh_key_id", "GET", $cloudProvider->access_key);

                if (isset($sshKeyData['error'])) {
                    $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $providerSshKey->name);

                    if (isset($sshKeyData['error'])) {
                        // ❌ Error response
                        return response()->json([
                            'message' => $sshKeyData['message']
                        ],500);
                    }

                    $sshKeyData = json_decode($sshKeyData);
                    $providerSshKey->ssh_key_id = $sshKeyData->ssh_key->id;
                    $providerSshKey->save();
                }
            }

            return $this->getResponse($user, $provider);

        } catch (\Exception $e) {
            report($e);
            if($e->getMessage()) {
                return response()->json([
                    'message' => $e->getMessage()
                ],500);
            }
        }
    }

    /**
     * Generate SSH key pair and store private key on disk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function generateKey($request)
    {
        $cloudProvider = CloudProvider::where('provider', $request->provider)->first();

        if(!$cloudProvider) {
            $cloudProviderKey = Helper::generateUniqueToken(64,'cloud_providers', 'key');
            $rsa = new RSA();
            $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);
            $keys = $rsa->createKey(2048);
            $private = $keys['privatekey'];
            $public = $keys['publickey'];
    
            Storage::disk('cloudProviderSSHKeys')->put($cloudProviderKey,$private);
            Storage::disk('cloudProviderSSHKeys')->put($cloudProviderKey.'.pub',$public);
        
            $cloudProvider = CloudProvider::updateOrCreate([
                'provider' => $request->provider],[
                'access_key' => $request->access_key,
                'access_secret' => $request->access_secret,
                'key' => $cloudProviderKey
            ]);
        } else {
            $public = Storage::disk('cloudProviderSSHKeys')->get($cloudProvider->key.".pub");

            $cloudProvider = CloudProvider::updateOrCreate([
                'provider' => $request->provider],[
                'access_key' => $request->access_key,
                'access_secret' => $request->access_secret
            ]);
        }
    
        return [
            'cloudProvider' => $cloudProvider,
            'public' => $public,
        ];
    }

    /**
     * Get response for linking a cloud provider account.
     *
     * @param  mixed  $user
     * @param  string  $provider
     * @return \Illuminate\Http\Response
     */
    public function getResponse($user, $provider)
    {
        Helper::adminActivity($user, 'Cloud Platform', 'Update', ucfirst($provider) . ' account has been linked.');

        return response()->json([
            'message' => "{$provider} account successfully linked.",
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Whitelabel\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    public function show(CloudProvider $cloudProvider)
    {
        // ❌ Error response
        return response()->json([
            'id' => $cloudProvider->id,
            'provider' => $cloudProvider->provider
        ],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    public function update(CloudProvider $cloudProvider)
    {
        try {
            $visible = !$cloudProvider->visible?"activated":"deactivated";
            $cloudProvider->visible = $cloudProvider->visible?false:true;
            $cloudProvider->save();

            Helper::adminActivity(auth()->user(), 'Cloud Platform', 'Update', ucfirst($cloudProvider->provider) . ' account is now ' . $visible . '.');

            return response()->json([
                'message' => ucfirst($cloudProvider->provider)." account is {$visible} successfully."
            ],200);
        } catch(Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Destroy the specified cloud provider.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    public function destroy(CloudProvider $cloudProvider) {
        try {
            $user = auth()->user();
            
            // Check if there are any existing servers associated with the cloud provider
            if ($cloudProvider->servers()->exists()) {
                return response()->json([
                    "message" => "Please delete the server before deleting the cloud platform!"
                ], 500);
            }

            $provider = $cloudProvider->provider;

            $cloudProvider->delete();

            Helper::adminActivity($user, 'Cloud Platform', 'Delete', ucfirst($provider) . ' account has been unlinked.');

            return response()->json([
                'message' => ucfirst($provider)." account successfully unlinked."
            ],200);

        } catch (Exception $e){
            // Log and handle exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Get regions available for the specified cloud provider.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    public function getRegions(CloudProvider $cloudProvider)
    {
        try {
            $user = auth()->user();

            // Determine the cloud provider and handle accordingly
            switch ($cloudProvider->provider) {
                case CloudProviderEnums::LIGHTSAIL():
                    return $this->handleLightsailRegions($cloudProvider);
                case CloudProviderEnums::DIGITALOCEAN():
                    return $this->handleDigitalOceanRegions($cloudProvider);
                case CloudProviderEnums::LINODE():
                    return $this->handleLinodeRegions($cloudProvider);
                case CloudProviderEnums::HETZNER():
                    return $this->handleHetznerRegions($cloudProvider);
                case CloudProviderEnums::VULTR():
                    return $this->handleVultrRegions($cloudProvider);
                default:
                    return $this->errorResponse('Invalid cloud platform!', 400);
            }
        } catch (Exception $e) {
            report($e);
            // ❌ Error response
            return response()->json([
                'message' => "Something went really wrong while fetching regions!"
            ], 500);
        }
    }

    /**
     * Handle fetching regions for Lightsail cloud provider.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    protected function handleLightsailRegions(CloudProvider $cloudProvider)
    {
        try {
            // Check if a specific region is requested
            if (request()->get('region')) {
                $region = request()->get('region');

                // Create a client for Lightsail API
                $client = Client::lightsail($cloudProvider->access_key, $cloudProvider->access_secret, $region);

                // Fetch regions with availability zones
                $data = $client->getRegions([
                    'includeAvailabilityZones' => true
                ]);

                // Find the requested region in the response
                $data = collect($data['regions'])->first(function ($row) use ($region) {
                    return $row['name'] == $region;
                });

                // ✅ Success response with region and availability zones
                return response()->json([
                    'region_zones' => $data['availabilityZones'],
                    'region' => $region,
                    'sizes' => null
                ], 200);
            } else {
                // Fetch all regions
                $client = Client::lightsail($cloudProvider->access_key, $cloudProvider->access_secret, 'ap-south-1');
                $data = $client->getRegions([
                    'includeAvailabilityZones' => true
                ]);

                // Filter and format the regions data
                $filteredRegions = collect($data['regions'])->map(function ($row) {
                    return [
                        "name" => $row['displayName'],
                        "value" => $row['name'],
                        "available" => true
                    ];
                })->filter();

                // ✅ Success response with filtered regions
                return response()->json($filteredRegions);
            }
        } catch (AwsException $aws) {
            // ❌ Error response for AWS exception
            report($aws);
            return response()->json([
                'message' => $aws->getAwsErrorMessage()
            ], 500);
        } catch (Exception $e) {
            // ❌ Error response for other exceptions
            report($e);
            return response()->json([
                'message' => "Something went really wrong while fetching Lightsail regions!"
            ], 500);
        }
    }

    /**
     * Handle fetching regions for Digitalocean cloud provider.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    protected function handleDigitalOceanRegions(CloudProvider $cloudProvider)
    {
        try {
            $data = Client::digitalOcean("regions", "GET", $cloudProvider->access_key);

            if (isset($data['error'])) {
                // ❌ Error response
                return response()->json([
                    'message' => $data['message']
                ], 500);
            }

            $data = json_decode($data);

            $data = collect($data->regions)->map(function ($row) {
                return [
                    "name" => $row->name,
                    "value" => $row->slug,
                    "available" => count($row->sizes) > 0,
                    "sizes" => $row->sizes
                ];
            });

            // ✅ Success response
            return response()->json($data);
        } catch (Exception $e) {
            // ❌ Error response
            report($e);
            return response()->json([
                'message' => "Something went really wrong while fetching DigitalOcean regions!"
            ], 500);
        }
    }

    /**
     * Handle fetching regions for Lightsail cloud provider.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    protected function handleLinodeRegions(CloudProvider $cloudProvider)
    {
        // Fetch regions from Linode API
        $data = Client::linode("regions", "GET", $cloudProvider->access_key);

        // Check for errors in the response
        if (isset($data['error'])) {
            // ❌ Error response
            return response()->json([
                'message' => $data['message']
            ], 500);
        }

        // Parse JSON response
        $data = json_decode($data);

        // Map the regions data
        $data = collect($data->data)->map(function ($row) {
            return [
                "name" => $row->label,
                "value" => $row->id,
                "available" => $row->status == "ok"?1:0,
                "sizes" => null
            ];
        });

        // ✅ Success response
        return response()->json($data);
    }

    /**
     * Handle fetching regions for Hetzner cloud provider.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    protected function handleHetznerRegions(CloudProvider $cloudProvider)
    {
        // Fetch regions from Hetzner API
        $data = Client::hetzner("locations", "GET", $cloudProvider->access_key);

        // Check for errors in the response
        if (isset($data['error'])) {
            // ❌ Error response
            return response()->json([
                'message' => $data['message']
            ], 500);
        }

        // Parse JSON response
        $data = json_decode($data);

        // Map the regions data
        $data = collect($data->locations)->map(function ($row) {
            return [
                "name" => $row->city,
                "value" => $row->name,
                "zone" => $row->network_zone,
                "available" => true
            ];
        });

        // ✅ Success response
        return response()->json($data);
    }

    /**
     * Handle fetching regions for Vultr cloud provider.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    protected function handleVultrRegions(CloudProvider $cloudProvider)
    {
        // Fetch regions from Vultr API
        $data = Client::vultr("regions", "GET", $cloudProvider->access_key);

        // Check for errors in the response
        if (isset($data['error'])) {
            // ❌ Error response
            return response()->json([
                'message' => $data['message']
            ], 500);
        }

        // Parse JSON response
        $data = json_decode($data);

        // Map the regions data
        $data = collect($data->regions)->map(function ($row) {
            return [
                "name" => $row->city,
                "value" => $row->id,
                "available" => true,
                "sizes" => null
            ];
        });

        // ✅ Success response
        return response()->json($data);
    }

    /**
     * Get size available for the specified cloud provider.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @return \Illuminate\Http\Response
     */
    public function getSizes(CloudProvider $cloudProvider)
    {
        try {
            $user = auth()->user();
            $region = request()->get('region');

            if ($cloudProvider->provider == CloudProviderEnums::LIGHTSAIL()) {
                return $this->handleLightsailSizes($cloudProvider, $region);
            } elseif ($cloudProvider->provider == CloudProviderEnums::DIGITALOCEAN()) {
                return $this->handleDigitalOceanSizes($cloudProvider, $region);
            } elseif ($cloudProvider->provider == CloudProviderEnums::VULTR()) {
                return $this->handleVultrSizes($cloudProvider, $region);
            } elseif ($cloudProvider->provider == CloudProviderEnums::LINODE()) {
                return $this->handleLinodeSizes($cloudProvider, $region);
            } elseif ($cloudProvider->provider == CloudProviderEnums::HETZNER()) {
                return $this->handleHetznerSizes($cloudProvider, $region);
            } else {
                // ❌ Not Found
                return response()->json([
                    'message' => "Server Provider not found!"
                ],404);
            }
        } catch (Exception $e) {
            report($e);
            // ❌ Error response
            return response()->json([
                'message' => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Handle fetching sizes for Lightsail cloud provider in the specified region.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @param  string  $region
     * @return \Illuminate\Http\Response
     */
    protected function handleLightsailSizes(CloudProvider $cloudProvider, $region)
    {
        try {
            if ($region) {
                // Instantiate Lightsail client
                $client = Client::lightsail($cloudProvider->access_key, $cloudProvider->access_secret, $region);
                // Get bundles from Lightsail
                $data = $client->getBundles([
                    'includeInactive' => false
                ]);

                // Filter bundles for LINUX_UNIX platform
                $data = collect($data['bundles'])->filter(function($row){
                    return in_array("LINUX_UNIX",$row['supportedPlatforms']) && $row['publicIpv4AddressCount'];
                });

                // Map bundle data to desired format
                $sizes = collect($data)->map(function($row){
                    $sizeInMb = $row['ramSizeInGb']*1024;
                    return [
                        'name'=>$row['name'],
                        'slug'=>$row['bundleId'],
                        'ram_size_in_mb'=> "{$sizeInMb}",
                        'cpu_core' => "{$row['cpuCount']}",
                        'disk_size_in_gb' => "{$row['diskSizeInGb']}",
                        'price' => $row['price'],
                        'bandwidth' => $row['transferPerMonthInGb']
                    ];
                });

                // Group sizes by name
                $data = $sizes->groupBy('name');

                // Reformat grouped data
                $sizes = $data->map(function($row, $key){
                    return [
                        'name'=>$key,
                        'list'=>$row
                    ];
                })->values();

                // ✅ Success response
                return response()->json([
                    'sizes' => $sizes,
                    'region' => $region
                ],200);
            }

            // ❌ Error response for invalid request
            return response()->json([
                'message' => "Invalid request!"
            ],500);
        } catch(AwsException $aws) {
            // ❌ Error response for AWS exception
            report($aws);
            return response()->json([
                'message' => $aws->getAwsErrorMessage()
            ],500);
        }
    }

    /**
     * Handle fetching sizes for DigitalOcean cloud provider in the specified region.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @param  string  $region
     * @return \Illuminate\Http\Response
     */
    protected function handleDigitalOceanSizes(CloudProvider $cloudProvider, $region)
    {
        if ($region) {
            // Fetch sizes from DigitalOcean
            $data = Client::digitalOcean("sizes?per_page=80", "GET", $cloudProvider->access_key);
            $data = json_decode($data);
            $totalRecord = $data->meta->total;
            $perPage = 20;
            $requestSend = ceil($totalRecord/$perPage);
            $sizesArr = array();
            $page=0;

            // Loop through paginated requests
            for($i = 1; $i <= $requestSend; $i++) {
                $page++;
                $data1 = Client::digitalOcean("sizes?page=$page", "GET", $cloudProvider->access_key);
                if(isset($data1['error'])) {
                    // ❌ Error response for DigitalOcean API error
                    return response()->json([
                        'message' => $data1['message']
                    ],500);
                }
                $data1 = json_decode($data1);
                $sizesArr = array_merge($sizesArr,$data1->sizes);
            }

            // Filter sizes for the requested region
            $regionPlans = collect($sizesArr)->filter(function($row) use ($region) {
                return in_array($region,$row->regions);
            });

            // Map sizes to desired format
            $sizes = collect($regionPlans)->map(function($row){
                return [
                    'name'=>$row->description,
                    'slug'=>$row->slug,
                    'regions' => $row->regions,
                    'ram_size_in_mb'=> "{$row->memory}",
                    'cpu_core' => "{$row->vcpus}",
                    'disk_size_in_gb' => "{$row->disk}",
                    'price' => $row->price_monthly,
                    'bandwidth' => $row->transfer * 1000
                ];
            });

            // Group sizes by name
            $data = $sizes->groupBy('name');

            // Reformat grouped data
            $sizes = $data->map(function($row, $key){
                return [
                    'name'=>$key,
                    'list'=>$row
                ];
            })->values();

            // ✅ Success response
            return response()->json([
                'sizes' => $sizes,
                'region' => $region
            ],200);
        }

        // ❌ Error response for invalid request
        return response()->json([
            'message' => "Invalid request!"
        ],500);
    }

    /**
     * Handle fetching sizes for Vultr cloud provider in the specified region.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @param  string  $region
     * @return \Illuminate\Http\Response
     */
    protected function handleVultrSizes(CloudProvider $cloudProvider, $region)
    {
        if ($region) {
            // Fetch plans from Vultr
            $data = Client::vultr("plans", "GET", $cloudProvider->access_key);
            if(isset($data['error'])) {
                // ❌ Error response for Vultr API error
                return response()->json([
                    'message' => $data['message']
                ],500);
            }

            $data = json_decode($data);

            // Filter plans for the requested region
            $regionPlans = collect($data->plans)->filter(function($row) use ($region) {
                return in_array($region,$row->locations);
            });

            // Map plans to desired format
            $sizes = collect($regionPlans)->map(function($row){
                if($row->type == "vhf") {
                    $name = "High Frequency";
                } else if($row->type == "vdc") {
                    $name = "Dedicated Cloud";
                } else {
                    $name = "Cloud Compute";
                }

                return [
                    'name'=>$name,
                    'slug'=>$row->id,
                    'regions' => $row->locations,
                    'ram_size_in_mb'=> "{$row->ram}",
                    'cpu_core' => "{$row->vcpu_count}",
                    'disk_size_in_gb' => "{$row->disk}",
                    'price' => $row->monthly_cost,
                    'bandwidth' => $row->bandwidth
                ];
            });

            // Group sizes by name
            $data = $sizes->groupBy('name');

            // Reformat grouped data
            $sizes = $data->map(function($row, $key){
                return [
                    'name'=>$key,
                    'list'=>$row
                ];
            })->values();

            // ✅ Success response
            return response()->json([
                'sizes' => $sizes,
                'region' => $region
            ],200);
        }

        // ❌ Error response for invalid request
        return response()->json([
            'message' => "Invalid request!"
        ],500);
    }

    /**
     * Handle fetching sizes for Linode cloud provider in the specified region.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @param  string  $region
     * @return \Illuminate\Http\Response
     */
    protected function handleLinodeSizes(CloudProvider $cloudProvider, $region)
    {
        if ($region) {
            // Fetch Linode plans
            $data = Client::linode("linode/types", "GET", $cloudProvider->access_key);
            
            if(isset($data['error'])) {
                // ❌ Error response for Linode API error
                return response()->json([
                    'message' => $data['message']
                ],500);
            }

            $data = json_decode($data);

            // Map Linode plans to desired format
            $sizes = collect($data->data)->map(function($row) {
                if($row->class == "standard" || $row->class == "nanode") {
                    $name = "Shared CPU";
                } else if($row->class == "highmem") {
                    $name = "High Memory";
                } else if($row->class == "dedicated") {
                    $name = "Dedicated CPU";
                } else {
                    $name = "GPU";
                }

                return [
                    'name'=>$name,
                    'label'=>$row->label,
                    'slug'=>$row->id,
                    'ram_size_in_mb'=> $row->memory,
                    'cpu_core' => $row->vcpus,
                    'disk_size_in_gb' => $row->disk/1024,
                    'price' => $row->price->monthly,
                    'bandwidth' => $row->transfer
                ];
            });

            // Group sizes by name
            $data = $sizes->groupBy('name');

            // Reformat grouped data
            $sizes = $data->map(function($row, $key){
                return [
                    'name'=>$key,
                    'list'=>$row
                ];
            })->values();

            // ✅ Success response
            return response()->json([
                "sizes" => $sizes     
            ]);
        } else {
            // ❌ Error response for invalid request
            return response()->json([
                'message' => "Invalid request!"
            ],500);
        }
    }

    /**
     * Handle fetching sizes for Hetzner cloud provider in the specified region.
     *
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @param  string  $region
     * @return \Illuminate\Http\Response
     */
    protected function handleHetznerSizes(CloudProvider $cloudProvider, $region)
    {
        if ($region) {
            // Fetch server types from Hetzner
            $data = Client::hetzner("server_types", "GET", $cloudProvider->access_key);
            if(isset($data['error'])) {
                // ❌ Error response for Hetzner API error
                return response()->json([
                    'message' => $data['message']
                ],500);
            }

            $data = json_decode($data);

            // Filter server types for the requested region
            $sizes = collect($data->server_types)->map(function($row) use ($region){
                if($row->cpu_type == "shared") {
                    $name = "Standard";
                } else if($row->cpu_type == "dedicated") {
                    $name = "Dedicated CPU";
                }

                $price = collect($row->prices)->where('location',$region)->values();
                
                if(sizeof($price) > 0 && $row->architecture!='arm') {
                    $basePrice = $price[0]->price_monthly->net;
                    $priceWithSurcharge = $basePrice + 0.50; // Add 0.50 euros surcharge
                    $bandwidth = $price[0]->included_traffic / 1099511627776; // Convert included_traffic to TB

                    return [
                        'name'=>$name,
                        'label'=>strtoupper($row->name),
                        'slug'=>$row->id,
                        'ram_size_in_mb'=> $row->memory*1024,
                        'cpu_core' => $row->cores,
                        'disk_size_in_gb' => $row->disk,
                        'price' =>  round($priceWithSurcharge, 2),
                        'bandwidth' => $bandwidth
                    ];
                }
            });

            // Group sizes by name
            $data = $sizes->where('name','!=',"")->groupBy('name');

            // Reformat grouped data
            $sizes = $data->map(function($row, $key){
                return [
                    'name'=>$key,
                    'list'=>$row->sortBy('price')->values()
                ];
            })->values();

            // ✅ Success response
            return response()->json([
                "sizes" => $sizes     
            ]);
        }

        // ❌ Error response for invalid request
        return response()->json([
            'message' => "Invalid request!"
        ],500);           
    }
}
