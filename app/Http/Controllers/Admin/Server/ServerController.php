<?php

namespace App\Http\Controllers\Admin\Server;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Interfaces\Server\ServerRepositoryInterface;
use App\Http\Utilities\Client;
use App\Http\Helper;
use Exception;
use App\Models\User;

class ServerController extends Controller
{
    private $serverRepository;

    /**
     * Create a new instance of the controller.
     *
     * @param  \App\Repositories\ServerRepositoryInterface  $serverRepository
     * @return void
     */
    public function __construct(ServerRepositoryInterface $serverRepository){
        $this->serverRepository = $serverRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        try {
            $search = request()->get('search');

            // Initialize the query
            $query = Server::query();

            // Apply the search criteria if provided
            if ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereAny(['name', 'ip'], 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->whereAny(['name', 'email'], 'like', "%{$search}%");
                        });
                });
            }

            // Filter by user ID if provided
            if (request()->get('user_id')) {
                $query->where('user_id', request()->get('user_id'));
            }

            // Execute the query and paginate results
            $servers = $query->select('id', 'name', 'cloud_provider_id', 'ip', 'user_id', 'plan_id', 'web_server', 'database_type', 'agent_status', 'country_code', 'ssh_port', 'created_at')
                ->with(['plan:id,ram,bandwidth,disk,cores', 'user:id,email,name,status', 'cloudProvider:id,provider'])
                ->orderByDesc('created_at')
                ->paginate(request()->input('per_page'));

            // Return JSON response with the paginated servers
            return response()->json([
                "servers" => $servers
            ],200);
        } catch (\Exception $e) {
            // Log the exception and return JSON response with error message
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }

    /**
     * Display the specified server.
     *
     * @param  \App\Models\Server\Server  $server
     * @return \Illuminate\Http\Response
     */
    public function show(Server $server){
        try {
            // Fetch the server details from the repository and return a successful response with server details
            return response($this->serverRepository->getServer($server));
        } catch (\Exception $e) {
            // Log the exception and return JSON response with error message
            report($e);
            return response()->json([
                "message" =>"Something went really wrong while fetching server!"
            ],500);
        }
    }

    /**
     * Remove the specified server.
     *
     * @param  \App\Models\Server\Server  $server
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Server $server){
        try {
            // Store the server's IP address
            $ip = $server->ip;

            // Deleting the server
            Helper::handleServerDeletion($server, false);

            // Store Admin Activity
            Helper::adminActivity(auth()->user(), 'Server', 'Delete', 'Server with IP (' . $ip . ') has been deleted.');

            // Return a JSON response: Server deleted successfully.
            return response()->json([
                "message" => "Server deleted successfully."
            ],200);
        } catch (\Exception $e) {
            // Log the exception and return JSON response with error message
            report($e);
            return response()->json([
                "message" => "Something went really wrong!"
            ],500);
        }
    }
}
