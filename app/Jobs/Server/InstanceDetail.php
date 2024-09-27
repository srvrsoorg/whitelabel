<?php

namespace App\Jobs\Server;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Server\Server;
use App\Models\Admin\Configuration\Plan;
use App\Models\Admin\SiteSetting;
use App\Enums\CloudProvider as CloudProviderEnum;
use App\Jobs\Server\InstanceInstall;
use App\Jobs\HostingUserDetail;
use App\Http\Utilities\Client;
use App\Http\Helper;
use Aws\Exception\AwsException;

class InstanceDetail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $server, $plan;
    /**
     * Create a new job instance.
     */
    public function __construct(Server $server, Plan $plan)
    {
        $this->server = $server;
        $this->plan = $plan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $server = $this->server;
        $serverInstance = $server->serverInstance;
        $cloudProvider = $serverInstance->cloudProvider;
        if(!$cloudProvider) {
            $this->fail("Cloud provider not found for server ID {$server->id}.");
        }
        
        $plan = $this->plan;
        $organizationId = SiteSetting::value('sa_org_id');
        if(!$cloudProvider) {
            $this->fail("Cloud platform not found.");
        }

        try {
            if($cloudProvider->provider == CloudProviderEnum::LIGHTSAIL()) {
                $client = Client::lightsail($cloudProvider->access_key, $cloudProvider->access_secret, $serverInstance->region);
                \Log::info("Server instance details.");
                try {
                    // Get instance detail
                    $getInstance = $client->getInstance(["instanceName" => $serverInstance->name]);
                    $getInstance = collect($getInstance["instance"]);
                    // Instance detail
                    $instanceId = null;
                    $ipv4Address = $getInstance["publicIpAddress"];
                    $cpuCout = isset($getInstance["hardware"]["cpuCount"])?$getInstance["hardware"]["cpuCount"]:0;
                    $diskSize = isset($getInstance["hardware"]["disks"][0]["sizeInGb"])?$getInstance["hardware"]["disks"][0]["sizeInGb"]:0;
                    $ramMemory = isset($getInstance["hardware"]["ramSizeInGb"])?$getInstance["hardware"]["ramSizeInGb"]*1024:0;
                    $transferMonthly = isset($getInstance["networking"]["monthlyTransfer"]["gbPerMonthAllocated"])?$getInstance["networking"]["monthlyTransfer"]["gbPerMonthAllocated"]:0;

                    // Create Firewall ruleserverInstance
                    $client->putInstancePublicPorts([
                        'instanceName'=>$serverInstance->name, 
                        "portInfos"=> [
                            ["fromPort"=>22,"toPort" => 22, "protocol" => "tcp"],
                            ["fromPort" => 80,"toPort" => 80, "protocol" => "tcp"],
                            ["fromPort"=>443,"toPort" => 443, "protocol" => "tcp"],
                            ["fromPort"=>43210,"toPort"=>43210, "protocol" => "tcp"]
                        ]
                    ]);
                } catch (AwsException $aws) {
                    $server->metas()->updateOrCreate(['name' => 'error_message'], ['value' => $aws->getAwsErrorMessage()]);

                    $server->ssh_status = 'failed';
                    $server->agent_status = 'failed';
                    $server->save();

                    Client::deleteServerInstance($serverInstance);
                    $this->fail("Something went really wrong while creating server instance.");
                }
            } else if($cloudProvider->provider == CloudProviderEnum::DIGITALOCEAN()) {
                $dropletDetail = Client::digitalOcean("droplets/{$serverInstance->instance_id}", "GET", $cloudProvider->access_key);

                if(isset($dropletDetail['error'])) {
                    $server->metas()->updateOrCreate(['name' => 'error_message'], ['value' => $dropletDetail['message']]);

                    $server->ssh_status = 'failed';
                    $server->agent_status = 'failed';
                    $server->save();

                    Client::deleteServerInstance($serverInstance);
                    $this->fail("Something went really wrong while creating server instance.");
                }

                $dropletDetail = json_decode($dropletDetail);
                $cpuCout = $dropletDetail->droplet->vcpus;
                $diskSize = $dropletDetail->droplet->disk;
                $ramMemory = $dropletDetail->droplet->memory;
                $ipv4Address = $dropletDetail->droplet->networks->v4[0]->ip_address;
            } else if($cloudProvider->provider == CloudProviderEnum::VULTR()) {
                $instanceDetail = Client::vultr("instances/{$serverInstance->instance_id}", "GET", $cloudProvider->access_key);

                if(isset($instanceDetail['error'])) {
                    $server->metas()->updateOrCreate(['name' => 'error_message'], ['value' => $instanceDetail['message']]);

                    $server->ssh_status = 'failed';
                    $server->agent_status = 'failed';
                    $server->save();

                    Client::deleteServerInstance($serverInstance);
                    $this->fail("Something went really wrong while creating server instance.");
                }

                $instanceDetail = json_decode($instanceDetail);
                $cpuCout = $instanceDetail->instance->vcpu_count;
                $diskSize = $instanceDetail->instance->disk;
                $ramMemory = $instanceDetail->instance->ram;
                $ipv4Address = $instanceDetail->instance->main_ip;
            } else if ($cloudProvider->provider == CloudProviderEnum::LINODE()) {
                $instanceDetail = Client::linode("linode/instances/{$serverInstance->instance_id}", "GET", $cloudProvider->access_key);

                if(isset($instanceDetail['error'])) {
                    $server->metas()->updateOrCreate(['name' => 'error_message'], ['value' => $instanceDetail['message']]);

                    $server->ssh_status = 'failed';
                    $server->agent_status = 'failed';
                    $server->save();

                    Client::deleteServerInstance($serverInstance);
                    $this->fail("Something went really wrong while creating server instance.");
                }

                $instanceDetail = json_decode($instanceDetail);
                $cpuCout = $instanceDetail->specs->vcpus;
                $diskSize = $instanceDetail->specs->disk/1024;
                $ramMemory = $instanceDetail->specs->memory;
                $ipv4Address = $instanceDetail->ipv4[0];
            } else if ($cloudProvider->provider == CloudProviderEnum::HETZNER()) {
                $instanceDetail = Client::hetzner("servers/{$serverInstance->instance_id}", "GET", $cloudProvider->access_key);

                if(isset($instanceDetail['error'])) {
                    $server->metas()->updateOrCreate(['name' => 'error_message'], ['value' => $instanceDetail['message']]);

                    $server->ssh_status = 'failed';
                    $server->agent_status = 'failed';
                    $server->save();

                    Client::deleteServerInstance($serverInstance);
                    $this->fail("Something went really wrong while creating server instance.");
                }

                $instanceDetail = json_decode($instanceDetail);
                $cpuCout = $instanceDetail->server->server_type->cores;
                $diskSize = $instanceDetail->server->server_type->disk;
                $ramMemory = $instanceDetail->server->server_type->memory*1024;
                $ipv4Address = $instanceDetail->server->public_net->ipv4->ip;
            } else {
                $server->metas()->updateOrCreate(['name' => 'error_message'], ['value' => "Something went really wrong while creating server instance."]);

                $server->ssh_status = 'failed';
                $server->agent_status = 'failed';
                $server->save();

                Client::deleteServerInstance($serverInstance);
                $this->fail("Something went really wrong while creating server instance.");
            }

            $serverInstance->ip = $ipv4Address;
            $serverInstance->cpu = $cpuCout;
            $serverInstance->disk_size = $diskSize;
            $serverInstance->memory = $ramMemory;
            $serverInstance->status = 1;
            $serverInstance->save();

            $server->ip = $ipv4Address;
            $server->save();
            
            //serveravatar api key
            $requestData = [
                'organization_id' => $organizationId,
                'web_server' => $server->web_server,
                'database_type' => $server->database_type,
                'version' => $server->version,
                'nodejs' => $server->nodejs,
                'yarn' => $server->yarn,
                'name' => $server->name,
                'hostname' => $serverInstance->name,
                'root_password_available' => false,
                'ip' => $serverInstance->ip,
                'ssh_port' => 22,
                'forceCleanup' => 1,
            ];

            $serveravatarClient = Client::serveravatar('self-hosted/servers', 'POST', $requestData);

            if(isset($serveravatarClient['error'])) {
                Client::deleteServerInstance($serverInstance);
                $server->metas()->updateOrCreate(['name' => 'error_message'], ['value' => $serveravatarClient['message']]);
                $this->fail("ServerAvatar Server Error => ".$serveravatarClient['message']);
            }

            //Note: add filed based on serveravatar response
            $server->metas()->create(['name' => "install_command",'value' => $serveravatarClient['command']]);

            $server->sa_server_id = $serveravatarClient['server']['id'];
            $server->sa_org_id = $serveravatarClient['server']['organization_id'];
            $server->php_cli_version = $serveravatarClient['server']['php_cli_version'];
            $server->ssh_port = $serveravatarClient['server']['ssh_port'];
            $server->save();
           
            if($server->sa_server_id){
                HostingUserDetail::dispatch($server)->onQueue('default');
            }

            if($cloudProvider->provider == CloudProviderEnum::VULTR()) {
                InstanceInstall::dispatch($server, $plan)->onQueue('instanceInstall')->delay(now()->addSeconds(120));
            } else {
                InstanceInstall::dispatch($server, $plan)->onQueue('instanceInstall')->delay(now()->addSeconds(60));
            }

        } catch (\Exception $e) {
            report($e);
            if(!$server->metas()->where('name', 'error_message')->exists()) {
                $server->metas()->updateOrCreate(['name' => 'error_message'],['value' => $e->getMessage()]);
            }
            $server->ssh_status = 'failed';
            $server->agent_status = 'failed';
            $server->save();
            
            if($serverInstance) {
                Client::deleteServerInstance($serverInstance);
            }
            $this->fail($e->getMessage());
        }
    }
}