<?php

namespace App\Jobs\Server;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Server\Server;
use App\Http\Utilities\Client;
use App\Jobs\Server\ServerUpdate;
use DivineOmega\SSHConnection\SSHConnection;
use App\Models\Admin\Configuration\Plan;
use Log;
use Exception;
use App\Http\Helper;

class InstanceInstall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $server, $plan;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Server\Server  $server
     * @return void
     */
    public function __construct(Server $server, Plan $plan)
    {
        $this->server = $server;
        $this->plan = $plan;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $server = $this->server;
        $serverInstance = $server->serverInstance;
        $user = $server->user;

        try {
            // Update server status to inspecting
            $server->update([
                'ssh_status' => 'inspecting',
                'agent_status' => 'inspecting',
            ]);

            $cloudProvider = $serverInstance->cloudProvider;
            if (!$cloudProvider) {
                $this->fail("Cloud provider not found for server ID {$server->id}.");
            }

            // Determine the username based on the cloud provider
            $username = $cloudProvider->provider == 'lightsail' ? 'ubuntu' : 'root';

            // Execute the inspection script
            $inspectResult = $this->executeInspectScript($server, $username);

            if (!$inspectResult) {
                Helper::handleServerDeletion($server);
                $this->fail("InstanceInstall :: Server ID {$server->id} :: Inspection script execution failed.");
            }
        } catch (Exception $e) {
            report($e);
            Helper::handleServerDeletion($server);
            $this->fail("InstanceInstall :: Server ID {$server->id} :: Exception occurred: {$e->getMessage()}");
        }
    }

    /**
     * Execute the inspection script on the server.
     *
     * @param  \App\Models\Server\Server  $server
     * @param  string  $username
     */
    private function executeInspectScript(Server $server, string $username)
    {
        $commands = $server->metas()->where('name', 'install_command')->first()->value;
        $keyPath = storage_path('app/cloud_provider_ssh_keys/' . $server->cloudProvider->key);

        try {
            $connection = (new SSHConnection())
                ->to($server->ip)
                ->onPort(22)
                ->as($username)
                ->withPrivateKey($keyPath)
                ->timeout(5000)
                ->connect();

            $command = $connection->run($commands);

            if ($command->getOutput()) {
                Log::info("executeInspectScript :: Server ID {$server->id} :: Success");
                $server->update(['ssh_status' => 'Successful execution of inspection script.']);

                // Server detail update
                ServerUpdate::dispatch($server, $this->plan)->onQueue('server')->delay(now()->addMinutes(10));
                return true;
            } else {
                Log::info("executeInspectScript :: Server ID {$server->id} :: Error");
                $server->update(['ssh_status' => 'There was an error processing server!']);
                return false;
            }
        } catch (Exception $e) {
            report($e);
            Log::error("executeInspectScript :: Server ID {$server->id} :: Exception occurred: {$e->getMessage()}");
            $server->update(['ssh_status' => 'Unable to connect to server, please try again!']);
            return false;
        }
    }
}