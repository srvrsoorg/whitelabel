<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Server\Server;
use App\Http\Utilities\Client;
use Str;


class HostingUserDetail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $server;

    /**
     * Create a new job instance.
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $server = $this->server;
            $response = Client::serveravatar("self-hosted/hosting-username", 'GET');

            $username = isset($response['username']) ? $response['username'] : Str::random(16);
            
            $requestData = [
                'username' => $username,
                'password' => Str::random(16),
            ];

            $hostingUser = Client::serveravatar("organizations/{$server->sa_org_id}/servers/{$server->sa_server_id}/hosting-user", 'POST', $requestData);
            
        } catch (\Exception $e) {
            report($e);
            $this->fail($e->getMessage());
        }

    }
}
