<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Configuration\CloudProvider;
use App\Models\Admin\{SiteSetting, BillingDetail};
use App\Models\Server\Server;
use App\Enums\CloudProvider as CloudProviderEnum;
use App\Jobs\Server\InstanceDetail;
use App\Http\Utilities\Client;
use App\Http\Helper;
use App\Rules\{MernDatabaseTypeRule, MernNodejsRule, ServerNameRule};
use App\Interfaces\Server\ServerRepositoryInterface;
use Exception, Storage;

class ServerController extends Controller
{
    // Server provider ubuntu version
    private $lightsailUbuntuVersion = ["20" => 'ubuntu_20_04', "22" => 'ubuntu_22_04'];
    private $digitalOceanUbuntuVersion = ["20" => 'ubuntu-20-04-x64', "22" => 'ubuntu-22-04-x64',  "24" => 'ubuntu-24-04-x64'];
    private $vultrUbuntuVersion = ["20" => 387, "22" => 1743, "24" => 2284];
    private $linodeUbuntuVersion = ["20" => 'linode/ubuntu20.04', "22" => 'linode/ubuntu22.04', "24" => 'linode/ubuntu24.04'];
    private $hetznerUbuntuVersion = ["20" => 'ubuntu-20.04', "22" => 'ubuntu-22.04', "24" => 'ubuntu-24.04'];

    private $serverRepository;

    /**
     * Create a new instance of the controller.
     *
     * @param  \App\Repositories\ServerRepositoryInterface  $serverRepository
     * @return void
     */
    public function __construct(ServerRepositoryInterface $serverRepository)
    {   
        // Set the product name from the application configuration
        $this->productName = config('app.title');

        // Apply middleware to verify ownership of the resource
        $this->middleware('verifyOwnership');

        // Inject the server repository dependency
        $this->serverRepository = $serverRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Retrieve the search query from the request
            $search = request()->get('search');
            
            // Retrieve the authenticated user
            $user = auth()->user();
            
            // Retrieve servers associated with the user, along with their plans
            $servers = $user->servers()->with(['plan'])->when($search, function($query) use ($search) {
                // Filter servers by name if search query is provided
                $query->where('name', 'like', '%' . $search . '%');
            })->orderByDesc('created_at')
            ->paginate(request()->input('per_page'));

            // Return JSON response with the paginated servers
            return response()->json(['servers' => $servers], 200);
        } catch (Exception $e) {
            // Log the exception
            report($e);
            
            // Return JSON response with error message
            return response()->json(['message' => "Error occurred while fetching servers! Please try again later."], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Check if any billing details exist in the database
        if(!BillingDetail::exists()) {
            return response()->json(['message' => 'Billing details have not been set! Please contact the administrator.'], 500);
        }

        // Retrieve the authenticated user
        $user = auth()->user();

        // Check if email not verify
        if(!$user->email_verified_at) {
            return response()->json(['message' => 'Your email address has not been verified! Please verify your email to proceed.'], 400);
        }

        // Validations
        $this->validateRequest($request);

        try {

            $requestedVersion = $request->version;

            // Check if the user has insufficient credits
            if ($user->credits <= 0) {
                return response()->json([
                    'message' => 'You cannot create a server due to insufficient credits.'
                ], 500);
            }

            // Retrieve cloud provider based on the requested provider
            $cloudProvider = CloudProvider::whereProvider($request->provider)->whereVisible(true)->first();

            // Check conditions
            $conditionCheck = $this->checkConditions($request, $cloudProvider, $user);
            if (!$conditionCheck['success']) {
                return response()->json([
                    'message' => $conditionCheck['message']
                ], $conditionCheck['status']);
            }

            $serverKeyName = Helper::generateUniqueToken(32, "cloud_provider_ssh_keys", "key");
            $keyName = Helper::generateUniqueSshKeyName();
            $sshKey = $cloudProvider->key;
            $instanceName = Helper::renameInstanceName($cloudProvider->provider, $request->name);

            // Create Server Instance 
            if($cloudProvider->provider == CloudProviderEnum::LIGHTSAIL()) {
                // Call the new function to handle Lightsail instance creation
                $instanceId = $this->handleLightsailInstanceCreation($cloudProvider, $request, $sshKey, $keyName, $serverKeyName, $instanceName);
                
            } else if($cloudProvider->provider == CloudProviderEnum::DIGITALOCEAN()) {
                // Call the new function to handle DigitalOcean instance creation
                $instanceId = $this->handleDigitalOceanInstanceCreation($cloudProvider, $request, $sshKey, $keyName, $serverKeyName, $instanceName);

            } else if($cloudProvider->provider == CloudProviderEnum::LINODE()) {
                // Call the new function to handle Linode instance creation
                $instanceId = $this->handleLinodeInstanceCreation($cloudProvider, $request, $sshKey, $keyName, $serverKeyName, $instanceName);

            } else if($cloudProvider->provider == CloudProviderEnum::HETZNER()) {
                // Call the new function to handle Hetzner instance creation
                $instanceId = $this->handleHetznerInstanceCreation($cloudProvider, $request, $sshKey, $keyName, $serverKeyName, $instanceName);

            } else if($cloudProvider->provider == CloudProviderEnum::VULTR()) {
                // Call the new function to handle Vultr instance creation
                $instanceId = $this->handleVultrInstanceCreation($cloudProvider, $request, $sshKey, $keyName, $serverKeyName, $instanceName);

            }

            // Get the provider SSH key
            $providerSshKey = $cloudProvider->sshKeys()->first();
            if($cloudProvider->provider == CloudProviderEnum::LIGHTSAIL()) {
                $providerSshKey = $cloudProvider->sshKeys()->where('region', $request->region)->first();
            }

            // Get the Server Plan
            $plan = $cloudProvider->plans()->whereRegion($request->region)->where("size_slug", $request->sizeSlug)->whereVisible(true)->first();

            // Create ssh key detail
            $serverInstance = $cloudProvider->serverInstances()->create([
                "instance_id" => $instanceId,
                "name" => $instanceName,
                "version" => $request->version,
                "key" => $providerSshKey->key,
                "region" => $request->region,
                "region_zone" => $request->availabilityZone
            ]);

            // Store Server Details
            $server = $serverInstance->server()->create([
                'user_id' => $user->id,
                "cloud_provider_id" => $cloudProvider->id,
                "plan_id" => $plan->id,
                "name" => $request->name,
                "ip" => "8.8.8.8",
                "provider_name" => $cloudProvider->provider,
                "web_server" => $request->web_server,
                "version" => $requestedVersion.".04",
                "key" => Helper::generateUniqueToken(32,'servers','key'),
                "nodejs" => $request->nodejs,
                "yarn" => $request->yarn?$request->yarn:0,
                "database_type" => $request->database_type,
                "php_cli_version" => "8.2"
            ]);

            // Determine the delay time in seconds based on the cloud provider
            if($cloudProvider->provider == CloudProviderEnum::VULTR()) {
                $delayTime = 300;  // If the cloud provider is VULTR
            } else {
                $delayTime = 150;  // For all other cloud providers
            }

            //job for server instance setup
            InstanceDetail::dispatch($server, $plan)->onQueue('instanceDetail')->delay(now()->addSeconds($delayTime));

            // Create activity
            Helper::createActivity(auth()->user(), 'Server', 'Create', 'Server ' . $server->name . ' has been created.');
            
            // Success response
            return response()->json([
                'message' => "The server creation process has been started.",
                'server' => $server
            ],200);
        } catch (Exception $e) {
            report($e);

            // Return an error response
            $message = $e->getMessage() ? $e->getMessage() : "Error occurred while creating server!";
            return response()->json(['message' => $message], 500);
        }
    }

    /**
     * Validate the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function validateRequest(Request $request)
    {
        // Retrieve list of visible cloud providers
        $providers = CloudProvider::where('visible', true)->pluck('provider')->toArray();

        // Validate request data
        $request->validate([
            "provider" => ["required", "in:" . implode(",", $providers)],
            "version" => "required|in:20,22,24",
            "name" => ['required', 'string', 'regex:/^[a-zA-Z0-9](?!.*--)(?!.*\.\.)[a-zA-Z0-9-\.]*$/', 'max:55', new ServerNameRule],
            "web_server" => "required|in:apache2,nginx,openlitespeed,mern",
            "database_type" => ['required', 'in:mysql,mariadb,mongodb', new MernDatabaseTypeRule],
            "nodejs" => ['required','boolean', new MernNodejsRule],
            "yarn" => "nullable|boolean",
            "region" => "required|string",
            "availabilityZone" => "nullable|required_if:provider,lightsail|string",
            "sizeSlug" => "required",
            "price" => "required|numeric",
            "linode_root_password" => ["nullable","required_if:provider,linode","string","min:11","max:128","regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{11,128}/"],
        ],[
            'region.required' => "The location field is required.",
            'availabilityZone.required_if' => 'The availability zone field is required.',
            'sizeSlug' => "Please select your plan.",
            'linode_root_password.required_if' => 'The linode root password field is required.',
            'linode_root_password.regex' => "Password does not meet strength requirement."
        ]);
    }

    /**
     * Check conditions for server creation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin\Configuration\CloudProvider  $cloudProvider
     * @param  \App\Models\User  $user
     * @return array
     */
    private function checkConditions($request, $cloudProvider, $user)
    {
        // Check if cloud provider exists
        if (!$cloudProvider) {
            return ['success' => false, 'message' => 'Cloud platform not found!', 'status' => 404];
        }

        // Check if selected plan exists for the specified region and size slug
        if (!$plan = $cloudProvider->plans()->whereRegion($request->region)->where("size_slug", $request->sizeSlug)->whereVisible(true)->first()) {
            return ['success' => false, 'message' => 'Selected plan not found!', 'status' => 404];
        }

        $providerVersions = $this->getProviderVersions();

        // Check if provider is valid
        if (!array_key_exists($request->provider, $providerVersions)) {
            return ['success' => false, 'message' => "Invalid provider!", 'status' => 500];
        }

        // Ensure the requested version is supported
        if (!array_key_exists($request->version, $providerVersions[$request->provider])) {
            return [
                'success' => false,
                'message' => "We support " . implode(', ', array_keys($providerVersions[$request->provider])) . " versions in " . ucfirst($request->provider) . "!",
                'status' => 500
            ];
        }

         // Check if version is supported
        $request->version = $providerVersions[$request->provider][$request->version];

        return ['success' => true];
    }

    /**
     * Get provider versions.
     *
     * @return array
     */
    private function getProviderVersions()
    {
        return [
            'lightsail' => $this->lightsailUbuntuVersion,
            'digitalocean' => $this->digitalOceanUbuntuVersion,
            'vultr' => $this->vultrUbuntuVersion,
            'linode' => $this->linodeUbuntuVersion,
            'hetzner' => $this->hetznerUbuntuVersion,
        ];
    }

    /**
     * Handle the creation of a Lightsail instance.
     *
     * @param  mixed  $cloudProvider An object or array containing cloud provider details.
     * @param  \Illuminate\Http\Request  $request The HTTP request containing the instance details.
     * @param  string  $sshKey The SSH key for instance access.
     * @param  string  $keyName The name of the SSH key.
     * @param  string  $serverKeyName The key name for the server.
     * @param  string  $instanceName The name of the Lightsail instance.
     * @return void
     */
    private function handleLightsailInstanceCreation($cloudProvider, $request, $sshKey, $keyName, $serverKeyName, $instanceName)
    {
        // Get the Lightsail client
        $client = Client::lightsail($cloudProvider->access_key, $cloudProvider->access_secret, $request->region);

        // Get the provider SSH key
        $providerSshKey = $cloudProvider->sshKeys()->where('region', $request->region)->first();

        // If provider SSH key doesn't exist, create it
        if ($providerSshKey == null) {
            $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $keyName, $request->region);
            if (isset($sshKeyData['error'])) {
                // Error response
                throw new \Exception($sshKeyData['message']);
            }

            // Create SSH key detail
            $providerSshKey = $cloudProvider->sshKeys()->create([
                "name" => $keyName,
                "ssh_key_id" => $sshKeyData['operation']['id'],
                "region" => $request->region,
                "key" => $serverKeyName
            ]);
        } else {
            // If provider SSH key exists, retrieve it
            try {
                $sshKeyData = $client->getKeyPair(["keyPairName" => $providerSshKey->name]);
            } catch (AwsException $aws) {
                // If retrieval fails, recreate the SSH key
                $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $providerSshKey->name, $request->region);
                if (isset($sshKeyData['error'])) {
                    // Error response
                    throw new \Exception($sshKeyData['message']);
                }

                $providerSshKey->ssh_key_id = $sshKeyData['operation']['id'];
                $providerSshKey->save();
            }
        }

        // Create instance in Lightsail
        try {
            $instance = $client->createInstances([
                'availabilityZone' => $request->availabilityZone,
                'blueprintId' => $request->version,
                'bundleId' => $request->sizeSlug,
                'instanceNames' => [$instanceName],
                'keyPairName' => $providerSshKey->name,
                'tags' => [
                    [
                        'key' => $this->productName,
                        'value' => "Created by " . $this->productName . ".",
                    ],
                ]
            ]);

            $instanceId = null;

            // Return the instance ID or any other relevant data
            return $instanceId;
        } catch (AwsException $aws) {
            report($aws);
            // Error response
            throw new \Exception($aws->getAwsErrorMessage());
        }
    }

    /**
     * Handle the creation of a DigitalOcean instance.
     *
     * @param  mixed  $cloudProvider An object or array containing cloud provider details.
     * @param  \Illuminate\Http\Request  $request The HTTP request containing instance details.
     * @param  string  $sshKey The SSH key for instance access.
     * @param  string  $keyName The name of the SSH key.
     * @param  string  $serverKeyName The key name for the server.
     * @param  string  $instanceName The name of the DigitalOcean instance.
     * @return void
     */
    private function handleDigitalOceanInstanceCreation($cloudProvider, $request, $sshKey, $keyName, $serverKeyName, $instanceName)
    {
        // Get the provider SSH key
        $providerSshKey = $cloudProvider->sshKeys()->first();

        // If provider SSH key doesn't exist, create it
        if ($providerSshKey == null) {
            $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $keyName);
            if (isset($sshKeyData['error'])) {
                // Error response
                throw new \Exception($sshKeyData['message']);
            }

            $sshKeyData = json_decode($sshKeyData);

            // Create SSH key detail
            $providerSshKey = $cloudProvider->sshKeys()->create([
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
                    // Error response
                    throw new \Exception($sshKeyData['message']);
                }
                $sshKeyData = json_decode($sshKeyData);
                $providerSshKey->ssh_key_id = $sshKeyData->ssh_key->id;
                $providerSshKey->save();
            }
        }

        // Create instance in DigitalOcean
        try {
            $requestData = [
                'name' => $instanceName,
                'region' => $request->region,
                'size' => $request->sizeSlug,
                'image' => $request->version,
                'ssh_keys' => [$providerSshKey->ssh_key_id],
                'backups' => false,
                'ipv6' => false
            ];

            $dropletInstance = Client::digitalOcean("droplets", "POST", $cloudProvider->access_key, json_encode($requestData));

            if (isset($dropletInstance['error'])) {
                // Error response
                $message = isset($dropletInstance['message']) ? $dropletInstance['message'] : "Error occurred while creating digitalocean instance!";
                throw new \Exception($message);
            }

            $dropletInstance = json_decode($dropletInstance);
            $instanceId = $dropletInstance->droplet->id;

            // Return the instance ID or any other relevant data
            return $instanceId;
        } catch (\Exception $e) {
            report($e);
            // Error response
            $message = $e->getMessage() ? $e->getMessage() : "Error occurred while creating digitalocean instance!";
            throw new \Exception($message);
        }
    }

    /**
     * Handle the creation of a Linode instance using the provided cloud provider details.
     *
     * @param  mixed  $cloudProvider An object or array containing cloud provider details.
     * @param  \Illuminate\Http\Request  $request The HTTP request containing instance details.
     * @param  string  $sshKey The SSH key for instance access.
     * @param  string  $keyName The name of the SSH key.
     * @param  string  $serverKeyName The key name used for the server.
     * @param  string  $instanceName The name to assign to the Linode instance.
     * @return void
     */
    private function handleLinodeInstanceCreation($cloudProvider, $request, $sshKey, $keyName, $serverKeyName, $instanceName)
    {
        // Get the provider SSH key
        $providerSshKey = $cloudProvider->sshKeys()->first();

        // If provider SSH key doesn't exist, create it
        if ($providerSshKey == null) {
            $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $keyName);
            if (isset($sshKeyData['error'])) {
                // Error response
                throw new \Exception($sshKeyData['message']);
            }
            $sshKeyData = json_decode($sshKeyData);

            // Create SSH key detail
            $providerSshKey = $cloudProvider->sshKeys()->create([
                "name" => $keyName,
                "ssh_key_id" => $sshKeyData->id,
                "key" => $serverKeyName
            ]);

        } else {
            $sshKeyData = Client::linode("profile/sshkeys/$providerSshKey->ssh_key_id", "GET", $cloudProvider->access_key);

            if (isset($sshKeyData['error'])) {
                $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $providerSshKey->name);
                if (isset($sshKeyData['error'])) {
                    // Error response
                    throw new \Exception($sshKeyData['message']);
                }

                $sshKeyData = json_decode($sshKeyData);
                $providerSshKey->ssh_key_id = $sshKeyData->id;
                $providerSshKey->save();
            }
        }

        // Create instance in Linode
        try {
            $requestData = [
                'label' => $instanceName,
                'region' => $request->region,
                'type' => $request->sizeSlug,
                'image' => $request->version,
                'authorized_keys' => [Storage::disk('cloudProviderSSHKeys')->get($cloudProvider->key . '.pub')],
                'root_pass' => $request->linode_root_password
            ];

            $linodeInstance = Client::linode("linode/instances", "POST", $cloudProvider->access_key, json_encode($requestData));

            if (isset($linodeInstance['error'])) {
                // Error response
               $message = isset($linodeInstance['message']) ? $linodeInstance['message'] : "Error occurred while creating linode instance!";
                throw new \Exception($message);
            }

            $linodeInstance = json_decode($linodeInstance);

            $instanceId = $linodeInstance->id;

            // Return the instance ID or any other relevant data
            return $instanceId;
        } catch (\Exception $e) {
            report($e);
            // Error response
            $message = $e->getMessage() ? $e->getMessage() : "Error occurred while creating linode instance!";
            throw new \Exception($message);
        }
    }

    /**
     * Handle the creation of a Hetzner instance using the provided cloud provider details.
     *
     * @param  mixed  $cloudProvider An object or array containing cloud provider details.
     * @param  \Illuminate\Http\Request  $request The HTTP request containing instance details.
     * @param  string  $sshKey The SSH key for instance access.
     * @param  string  $keyName The name of the SSH key.
     * @param  string  $serverKeyName The key name used for the server.
     * @param  string  $instanceName The name to assign to the Linode instance.
     * @return void
     */
    private function handleHetznerInstanceCreation($cloudProvider, $request, $sshKey, $keyName, $serverKeyName, $instanceName)
    {
        // Get the provider SSH key
        $providerSshKey = $cloudProvider->sshKeys()->first();

        // If provider SSH key doesn't exist, create it
        if ($providerSshKey == null) {
            $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $keyName);
            if (isset($sshKeyData['error'])) {
                // Error response
                throw new \Exception($sshKeyData['message']);
            }
            $sshKeyData = json_decode($sshKeyData);

            // Create SSH key detail
            $providerSshKey = $cloudProvider->sshKeys()->create([
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
                    // Error response
                    throw new \Exception($sshKeyData['message']);
                }

                $sshKeyData = json_decode($sshKeyData);
                $providerSshKey->ssh_key_id = $sshKeyData->ssh_key->id;
                $providerSshKey->save();
            }
        }

        // Create instance in Hetzner
        try {
            $requestData = [
                'name' => $instanceName,
                'location' => $request->region,
                'server_type' => $request->sizeSlug,
                'image' => $request->version,
                'ssh_keys' => [(int)$providerSshKey->ssh_key_id],
            ];

            $hetznerInstance = Client::hetzner("servers", "POST", $cloudProvider->access_key, json_encode($requestData));

            if (isset($hetznerInstance['error'])) {
                // Error response
                $message = isset($hetznerInstance['message']) ? $hetznerInstance['message'] : "Error occurred while creating hetzner instance!"; 
                throw new \Exception($message);
            }

            $hetznerInstance = json_decode($hetznerInstance);
            $instanceId = $hetznerInstance->server->id;

            // Return the instance ID or any other relevant data
            return $instanceId;
        } catch (\Exception $e) {
            report($e);
            // Error response
            $message = $e->getMessage() ? $e->getMessage() : "Error occurred while creating hetzner instance!";
            throw new \Exception($message);
        }
    }

    /**
     * Handle the creation of a Vultr instance using the provided cloud provider details.
     *
     * @param  mixed  $cloudProvider An object or array containing cloud provider details.
     * @param  \Illuminate\Http\Request  $request The HTTP request containing instance details.
     * @param  string  $sshKey The SSH key for instance access.
     * @param  string  $keyName The name of the SSH key.
     * @param  string  $serverKeyName The key name used for the server.
     * @param  string  $instanceName The name to assign to the Linode instance.
     * @return void
     */
    private function handleVultrInstanceCreation($cloudProvider, $request, $sshKey, $keyName, $serverKeyName, $instanceName)
    {
        // Get the provider SSH key
        $providerSshKey = $cloudProvider->sshKeys()->first();

        // If provider SSH key doesn't exist, create it
        if ($providerSshKey == null) {
            $sshKeyData = Client::storeServerInstanceSshKey($sshKey, $cloudProvider, $keyName);

            if (isset($sshKeyData['error'])) {
                // Error response
                throw new \Exception($sshKeyData['message']);
            }
            $sshKeyData = json_decode($sshKeyData);

            // Create SSH key detail
            $providerSshKey = $cloudProvider->sshKeys()->create([
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
                    // Error response
                    throw new \Exception($sshKeyData['message']);
                }

                $sshKeyData = json_decode($sshKeyData);
                $providerSshKey->ssh_key_id = $sshKeyData->ssh_key->id;
                $providerSshKey->save();
            }
        }

        // Create instance in Vultr
        try {
            $requestData = [
                'hostname' => $instanceName,
                'region' => $request->region,
                'plan' => $request->sizeSlug,
                'os_id' => $request->version,
                'label' => $instanceName,
                'sshkey_id' => [$providerSshKey->ssh_key_id],
                'backups' => "disabled"
            ];

            $vultrInstance = Client::vultr("instances", "POST", $cloudProvider->access_key, json_encode($requestData));

            if (isset($vultrInstance['error'])) {
                // Error response
                $message = isset($vultrInstance['message']) ? $vultrInstance['message'] : "Error occurred while creating vultr instance!";
                throw new \Exception($message);
            }

            $vultrInstance = json_decode($vultrInstance);
            $instanceId = $vultrInstance->instance->id;

            // Return the instance ID or any other relevant data
            return $instanceId;
        } catch (\Exception $e) {
            report($e);
            // Error response
            $message = $e->getMessage() ? $e->getMessage() : "Error occurred while creating vultr instance!";
            throw new \Exception($message);
        }
    }

    /**
     * Display the specified server.
     *
     * @param  \App\Models\Server\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function show(Server $server)
    {
        try {
            // Return a response, for example, JSON or a view for displaying the server
            return response()->json($this->serverRepository->getServer($server));
        } catch (Exception $e) {
            // Log the exception
            report($e);
            
            // Return a response with an error message
            return response()->json(['message' => "Error occurred while fetching server details!"], 500);
        }
    }

    /**
     * Remove the specified server from storage.
     *
     * @param  \App\Models\Server\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function destroy(Server $server)
    {
        try {
            // Retrieve the authenticated user
            $user = auth()->user();

            // Store the IP address before deleting the server
            $ip = $server->ip;

            // Deleting the server
            Helper::handleServerDeletion($server, false);

            // Create activity
            Helper::createActivity($user, 'Server', 'Delete', 'Server (' . $ip . ') has been deleted.');
            
            // Return a success response
            return response()->json(['message' => 'Server deleted successfully.'], 200);
        } catch (Exception $e) {
            // Log the exception
            report($e);
            
            // Return an error response
            return response()->json(['message' => "Error occurred while deleting server!"], 500);
        }
    }

    /**
     * Reconnect the specified server.
     *
     * @param  \App\Models\Server\Server  $server
     * @return \Illuminate\Http\JsonResponse
     */
    public function serverReconnect(Server $server)
    {
        try {

            if(!$server->sa_server_id && in_array($server->agent_status, [0, 'failed'])) {
                return response()->json(['message' => 'Server creation failed, Please try again!'], 500);
            }

            if (!in_array($server->agent_status, [0, 'failed'])) {
                return response()->json(['message' => 'You cannot reconnect server!'], 500);
            }

            // Attempt to reconnect the server
            $response = Client::serveravatar("organizations/{$server->sa_org_id}/servers/{$server->sa_server_id}/reconnect", 'GET');

            // Handle any errors from the response
            if (isset($response['error'])) {
                return response()->json(['message' => $response['message']], 500);
            }

            // Update server status to indicate successful reconnection
            $server->update(['agent_status' => 1]);

            return response()->json(['message' => 'Server reconnected successfully.'], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['message' => 'Something went really wrong!'], 500);
        }
    }

    /**
     * Update the specified server status.
     *
     * @param  \App\Models\Server\Server  $server
     * @return \Illuminate\Http\JsonResponse
     */

    public function serverUpdate(Server $server)
    {
        try {

            // Check if the agent status is 'failed'
            if (in_array($server->agent_status, [0, 'failed'])) {
                Helper::handleServerDeletion($server);
                return response()->json(['message' => 'Server creation failed, Please try again!'], 500);
            }

            // Check if the error message
            $errorMessage = $server->metas()->where('name', 'error_message')->value('value');
            if($errorMessage) {
                Helper::handleServerDeletion($server);
                return response()->json(['message' => $errorMessage], 500);
            }

            // Check if the server has an associated server ID
            if (!$server->sa_server_id) {
                return response()->json(['response' => null], 200);
            }

            // Fetch server status from the client
            $response = Client::serveravatar("organizations/{$server->sa_org_id}/servers/{$server->sa_server_id}/status", 'GET');

            // Handle errors from the response
            if (isset($response['error'])) {
                Helper::handleServerDeletion($server);
                return response()->json(['message' => $response['message']], 500);
            }

            // Update server details if the agent status is not 1
            if ($response && $server->agent_status != 1) {
                $this->updateServerDetails($server, $response['server']);

                // Check if the agent status is 'failed'
                if (in_array($response['server']['agent_status'], [0, 'failed'])) {
                    $server->update(['agent_status' => 'failed' , 'ssh_status' => 'failed']);
                    Helper::handleServerDeletion($server);
                    return response()->json(['message' => 'Server creation failed, Please try again!'], 500);
                }
            }

            // Update server credentials if agent_status or ssh_status is 1
            if ($response && $response['server']['agent_status'] == 1) {
                $serverDetail = Client::serveravatar("self-hosted/servers/{$server->sa_server_id}", 'GET');
                $this->updateServerCredentials($server, $serverDetail['server']);
            }

            // Set Current Server Provider
            if(isset($response['server']['provider_name'])) {
                $response['server']['provider_name'] = $server->provider_name;
            }

            // Return the response in JSON format
            return response()->json(['response' => $response], 200);

        } catch (\Exception $e) {
            report($e);
            Helper::handleServerDeletion($server);
            return response()->json(['message' => 'Server creation failed, Please try again!'], 500);
        }
    }

    /**
     * Update the details of the specified server.
     *
     * @param  \App\Models\Server\Server  $server
     * @param  array  $serverData
     * @return void
     */
    private function updateServerDetails(Server $server, array $serverData)
    {
        $server->fill([
            'agent_version' => $serverData['agent_version'],
            'agent_status' => $serverData['agent_status'],
            'ssh_status' => $serverData['ssh_status'],
            'php_cli_version' => $serverData['php_cli_version'],
            'operating_system' => $serverData['operating_system'],
            'hostname' => $serverData['hostname'],
            'timezone' => $serverData['timezone'],
            'country_code' => $serverData['country_code'],
        ])->save();
    }

    /**
     * Update the server's credentials with the provided details.
     *
     * @param  \App\Models\Server\Server  $server
     * @param  array  $serverDetail
     * @return void
     */
    private function updateServerCredentials(Server $server, array $serverDetail)
    {
        $server->fill([
            'username' => $serverDetail['username'],
            'password' => $serverDetail['password'],
            'database_username' => $serverDetail['database_username'],
            'database_password' => $serverDetail['database_password'],
            'redis_password' => $serverDetail['redis_password'],
        ])->save();
    }
}
