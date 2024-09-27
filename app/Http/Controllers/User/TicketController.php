<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Ticket};
use Storage;
use App\Interfaces\TicketRepositoryInterface;
use App\Http\Helper;

class TicketController extends Controller
{
    /**
     * TicketController constructor.
     *
     * @param  TicketRepositoryInterface  $ticketRepository
     */
    public function __construct(protected TicketRepositoryInterface $ticketRepository){
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Display a listing of the tickets.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
        try {
            // Retrieve authenticated user's tickets from the repository with filters
            $tickets = $this->ticketRepository->index($request, auth()->user());

            // Success response with Tickets details 
            return response()->json($tickets);
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }

    /**
     * Store a newly created ticket in database.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request){
        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s]*$/',
            'server_id' => 'nullable|integer',
            'department' => 'required|string|max:255',
            'description' => 'required|string',
            'attachment' => 'nullable|mimes:jpeg,jpg,png,webp|max:1024',
        ],[
            'attachment.max' => 'The image must not be greater than 1 MB.'
        ]);

        try {
            // Retrieved authenticated user
            $user = auth()->user();

            // Create a new ticket
            $ticket = $user->tickets()->create([
                'title' => $request->title,
                'server_id' => $request->server_id,
                'department' => $request->department,
            ]);

            // Initialize $attachment variable
            $attachment = null;

            // Check if attachment is present
            if ($request->hasFile('attachment')) {
                // Generate unique filename for attachment
                $attachment = $request->attachment->getUuidName();

                // Store attachment in storage
                Storage::disk('public')->putFileAs('ticket', $request->attachment, $attachment);
            }

            // Create a reply if the ticket was created
            if (filled($ticket)) {
                $ticket->replies()->create([
                    'reply' => $request->description,
                    'attachment' => $attachment,
                ]);
            }

            // Create activity
            Helper::createActivity($user, 'Ticket', 'Create', 'Ticket (#' . $ticket->id . ') has been created.');

            // Success response
            return response()->json([
                "message" => "Ticket has been created successfully.",
            ],200);
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }

    /**
     * Display the specified ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Ticket $ticket){
        try {
            // Check if the authenticated user owns the ticket
            if(!auth()->user()->tickets()->where("id", $ticket->id)->exists()){
                return response()->json([
                    "message" => "You cannot access this ticket!",
                ],403);
            }

            // Retrieve the ticket details from the repository
            $ticket = $this->ticketRepository->show($ticket);

            // Success response with Ticket details
            return response()->json([
                "ticket" => $ticket,
            ],200);
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }

    /**
     * Update the status of the specified ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @param  string  $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function statusUpdate(Ticket $ticket, $action){
        try {
            // Check if the authenticated user owns the ticket
            if(!auth()->user()->tickets()->where("id", $ticket->id)->exists()){
                return response()->json([
                    "message" => "You cannot access this ticket!",
                ],403);
            }

            // Validate the action
            if (!in_array($action, ['open', 'closed'])) {
                return response()->json([
                    "message" => "Invalid status!",
                ],500);
            }
            
            // Check if the ticket is already in the given status
            if ($ticket->status == $action) {
                return response()->json([
                    "message" => "Ticket is already $action!"
                ],500);
            }

            Helper::createActivity(auth()->user(), 'Ticket', 'Update', 'Ticket (#' . $ticket->id . ') status has been updated to ' . $action . '.');

            // Update the status using the repository
            return response()->json($this->ticketRepository->statusUpdate($ticket, $action));
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Get a list of server IDs associated with the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function serverIds(){
        try {
            // fetch authenticated user's server details
            $serverIds = auth()->user()->servers()->where('agent_status', true)->select('id', 'name', 'ip')->get();

            // Success response with server details
            return response()->json([
                "server_ids" => $serverIds
            ],200);
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!"
            ],500);
        }
    }
}
