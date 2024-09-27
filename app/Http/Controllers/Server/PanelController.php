<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Utilities\Client;
use App\Models\Server\Server;
use Carbon\Carbon;

class PanelController extends Controller
{
    /**
     * Constructor to apply middleware for verifying server ownership
     */
    public function __construct()
    {
        $this->middleware('verifyOwnership');
    }

    /**
     * Display the server access panel details for the authenticated user.
     *
     * @param Server $server The server instance
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Server $server) {
        try {
            // Get the authenticated user
            $authenticatedUser = auth()->user();

            if($server->creditReminder && $server->creditReminder->due_at < today()){
                return response()->json([
                    "message" => "You cannot access this server during the retention period!"
                ],500);
            }

            // Fetch the panel user details from the ServerAvatar API
            $panelUserDetails = Client::serveravatar("organizations/{$server->sa_org_id}/servers/{$server->sa_server_id}/hosting-user", 'GET');

            if(isset($panelUserDetails['error'])){
                return response()->json([
                    "message" => $panelUserDetails['message'],
                ],500);
            }

            // Return the panel user details as a JSON response
            return response()->json(["panelUser" => $panelUserDetails]);

        } catch(\Exception $exception) {
            // Log the exception for debugging
            report($exception);
            
            // Return a JSON response with an error message
            return response()->json(['message' => "Error occurred while fetching access panel details! Please try again later."], 500);
        }
    }
}