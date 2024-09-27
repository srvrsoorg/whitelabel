<?php

namespace App\Jobs\Server;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Server\Server;
use App\Http\Utilities\Client;
use App\Models\Admin\Configuration\Plan;
use App\Http\Helper;
use App\Mail\Server\ServerCreated;

class ServerUpdate implements ShouldQueue
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
        try {
            $server = $this->server;
            if(!$server) {
                $this->fail("Server not found.");
            }

            $serverInstance = $server->serverInstance;

            $response = Client::serveravatar("self-hosted/servers/{$server->sa_server_id}", 'GET');

            if(isset($response['error'])){
                $this->fail($response['message']);
            }

            if($server->sa_server_id){
                $server->agent_version = $response['server']['agent_version'];
                $server->agent_status = $response['server']['agent_status'];
                $server->ssh_status = $response['server']['ssh_status'];
                $server->php_cli_version = $response['server']['php_cli_version'];
                $server->operating_system = $response['server']['operating_system'];
                $server->hostname = $response['server']['hostname'];
                $server->timezone = $response['server']['timezone'];
                $server->country_code = $response['server']['country_code'];
                $server->username = $response['server']['username'];
                $server->password = $response['server']['password'];
                $server->database_username = $response['server']['database_username'];
                $server->database_password = $response['server']['database_password'];
                $server->redis_password = $response['server']['redis_password'];
                $server->save();
            }

            if(in_array($response['server']['agent_status'], [0,'failed'])){
                Helper::handleServerDeletion($server);
                $this->fail("Agent status failed.");
            }

            // If agent_status is 1 and server_creation_mail has not been sent, send the email
            if (!$server->server_creation_mail) {
                \Mail::to($server->user)->queue((new ServerCreated($server))->onQueue('server'));
                
                // Check if the meta entry exists and create it if it doesn't
                $server->metas()->firstOrCreate(
                    ['name' => 'server_creation_mail_sent'],
                    ['value' => true]
                );
            }

            // Create server subscription
            $subscription = $server->subscription()->create([
                'user_id' => $server->user_id,
                'plan_id' => $this->plan->id,
                'amount' => $this->plan->price_per_month,
                'monthly_price' => $this->plan->price_per_month,
            ]);

        } catch (\Exception $e) {
            report($e);
            $this->fail($e->getMessage());
        }
    }
}
