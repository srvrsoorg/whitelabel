<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\TicketRepositoryInterface;
use App\Models\{Ticket, Reply, User};
use App\Http\Helper;

class ReplyController extends Controller
{
    /**
     * ReplyController constructor.
     *
     * @param  \App\Interfaces\TicketRepositoryInterface  $ticketRepository
     */
    public function __construct(protected TicketRepositoryInterface $ticketRepository){
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Display a listing of the replies for a specific ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Ticket $ticket){
        try {
            // Ensure the authenticated user owns the ticket
            if (!auth()->user()->tickets()->where("id", $ticket->id)->exists()) {
                return response()->json([
                    "message" => "You cannot access this ticket!",
                ],403);
            }

            // Retrieve replies from the repository
            $replies = $this->ticketRepository->replyIndex($ticket, auth()->user());

            // Success response with replies details
            return response()->json([
                "replies" => $replies
            ],200);   
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!"
            ],500);
        }
    }

    /**
     * Store a newly created reply in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Ticket $ticket){
        // Ensure the authenticated user owns the ticket
        if (!auth()->user()->tickets()->where("id", $ticket->id)->exists()) {
            return response()->json([
                "message" => "You cannot access this ticket!",
            ],403);
        }

        // Validate the request data
        $request->validate([
            'reply' => 'nullable|required_without:attachment',
            'attachment' => 'nullable|required_without:reply|mimes:jpeg,jpg,png,webp|max:1024',
        ],[
            'attachment.max' => 'The image must not be greater than 1 MB.',
        ]);

        try {
            if($ticket->status == 'closed'){
                return response()->json([
                    'message' => 'Ticket is closed! Please reopen it to reply!'
                ],500);
            }
            
            // Check if the user associated with the ticket is deleted
            if (!$ticket->user) {
                return response()->json([
                    'message' => 'The user associated with this ticket has been deleted! No more replies can be added!'
                ], 404);
            }

            // Store the reply using the repository
            $reply = $this->ticketRepository->storeReply($request, $ticket);

            // Create activity
            Helper::createActivity(auth()->user(), 'Ticket', 'Create', 'Reply has been created for Ticket #' . $ticket->id . '.');

            // Success response
            return response()->json([
                "message" => "Reply has been created successfully.",
            ],200);
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!",
            ],500);
        }
    }
}
