<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Ticket};
use App\Interfaces\TicketRepositoryInterface;
use App\Http\Helper;

class TicketController extends Controller
{
    /**
     * TicketController constructor.
     *
     * @param  \App\Interfaces\TicketRepositoryInterface  $ticketRepository
     */
    public function __construct(protected TicketRepositoryInterface $ticketRepository){
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Display a listing of the tickets.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request){
        try {
            // Retrieve tickets from the repository with filters
            $tickets = $this->ticketRepository->index($request);

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
     * Display the specified ticket.
     *
     * @param  \App\Models\Ticket  $ticket
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Ticket $ticket){
        try {
            // Retrieve the ticket details from the repository
            $ticket = $this->ticketRepository->show($ticket);

            // Success response with Ticket details
            return response()->json([
                "ticket" => $ticket
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
            // Validate the action
            if (!in_array($action, ['open', 'closed'])) {
                return response()->json([
                    "message" => "Invalid status!",
                ],500);
            }
            
            // Check if the ticket is already in the given status
            if ($ticket->status == $action) {
                return response()->json([
                    "message" => "Ticket is already $action."
                ],500);
            }

            Helper::adminActivity(auth()->user(), 'Ticket', 'Update', 'Ticket (#' . $ticket->id . ') status has been updated to ' . $action . '.');

            // Update the status using the repository
            return response($this->ticketRepository->statusUpdate($ticket, $action));
        } catch (\Exception $e) {
            // Log and handle exceptions
            report($e);
            return response()->json([
                "message" => "Something went really wrong!"
            ],500);
        }
    }
}
