<?php

namespace App\Repositories;

use App\Interfaces\TicketRepositoryInterface;
use App\Models\Ticket;
use Carbon\Carbon;
use Str, Storage;
use App\Http\Helper;

class TicketRepository implements TicketRepositoryInterface
{
	/**
     * Retrieve a list of tickets with optional filters and pagination.
     *
     * @param  \Illuminate\Http\Request  $request  The request object containing filters and pagination info.
     * @param  \App\Models\User|null  $user  The user to filter tickets by (optional).
     * @return array
     */
	public function index($request, $user = null)
	{
		// Build the base query for tickets
		$searchTerm = request()->get('search');

	    // Common query conditions for tickets
	    $ticketQueryConditions = function ($query) use ($searchTerm, $user) {
	        $query->whereHas('user')
                ->when($user, fn($query) => $query->where('user_id', $user->id))
                ->when($searchTerm, function ($subQuery) use ($searchTerm) {
                    $subQuery->where(function ($subQuery) use ($searchTerm) {
                        $subQuery->where('id', 'LIKE', '%' . $searchTerm . '%')
                            ->orWhere('title', 'LIKE', '%' . $searchTerm . '%');

                        if (Str::contains(request()->route()->uri(), 'admin')) {
                            $subQuery->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                                $userQuery->where('name', 'LIKE', '%' . $searchTerm . '%')
                                    ->orWhere('email', 'LIKE', '%' . $searchTerm . '%');
                        });
                    }
                });
            });
	    };

	    // Build the tickets query
	    $ticketsQuery = Ticket::select('id', 'user_id', 'title', 'department', 'status', 'created_at')
	        ->when($request->status == 'closed', fn($query) => $query->where('status', 'closed'))
	        ->when($request->status == 'open', fn($query) => $query->where('status', 'open'))
	        ->where($ticketQueryConditions)
	        ->with(['latestReply:id,ticket_id,support_agent_id,created_at'])
	        ->orderByRaw('(SELECT MAX(created_at) FROM replies WHERE ticket_id = tickets.id) DESC');

	    // Paginate tickets
	    $tickets = $ticketsQuery->when(auth()->user()->isSuperAdmin(), function ($query) {
	        return $query->with('user:id,name,avatar');
	    })->paginate($request->per_page);

	    // Count open and closed tickets
	    $openTicketsCount = Ticket::where('status', 'open')->where($ticketQueryConditions)->count();
	    $closedTicketsCount = Ticket::where('status', 'closed')->where($ticketQueryConditions)->count();
		
		// Use the map function to format the latestReply's created_at
        $tickets->map(function ($ticket) use ($user) {
        	if($user) {
        		$userRecord = $user;
        	} else {
        		$userRecord = auth()->user();
        	}

        	$ticket->formatted_created_at = $ticket->created_at ? Carbon::parse($ticket->created_at)->diffForHumans(now($userRecord->timezone)->toDateTimeString(),true).' ago' : null;
            
            if ($ticket->latestReply) {
            	$ticket->latestReply->reply_by = $ticket->latestReply?->support_agent_id ? "Support" : $ticket->user?->name;
            	$ticket->latestReply->created_at_human = Carbon::parse($ticket->latestReply->created_at)->diffForHumans(now($userRecord->timezone)->toDateTimeString(),true).' ago';
            }
            return $ticket;
        });

		return [
        	"open_tickets_count" => $openTicketsCount,
        	"closed_tickets_count" => $closedTicketsCount,
        	"tickets" => $tickets
        ];
	}

	/**
     * Retrieve details of a specific ticket, including its server and user if necessary.
     *
     * @param  \App\Models\Ticket  $ticket  The ticket to retrieve.
     * @return \App\Models\Ticket
     */
   	public function show($ticket)
   	{
		// Build the base query for a ticket with server details
		$ticketQuery = Ticket::where('id', $ticket->id)
			->select('*')
			->with('server:id,ip,name,agent_status');

		// Add user details if the authenticated user is a super admin
		if (Str::contains(request()->route()->uri, 'admin') && auth()->user()->isSuperAdmin()) {
			$ticket = $ticketQuery->with('user:id,name,email,avatar')->first();
		} else {
			$ticket = $ticketQuery->first();
		}

		// Add the first reply's description to the ticket
		$ticket['description'] = optional($ticket->replies()->first())->reply;

		return $ticket;
	}

    /**
     * Store a reply for a specific ticket, handling attachment if present.
     *
     * @param  \Illuminate\Http\Request  $request  The request object containing reply data and optional attachment.
     * @param  \App\Models\Ticket  $ticket  The ticket to add the reply to.
     * @return \App\Models\Reply
     */
   	public function storeReply($request, $ticket)
   	{
		// Initialize $attachment variable
        $attachment = null;

        // Check if attachment is present
        if ($request->hasFile('attachment')) {
            // Generate unique filename for attachment
            $attachment = $request->attachment->getUuidName();

            // Store attachment in storage
            Storage::disk('public')->putFileAs('ticket', $request->attachment, $attachment);
        }

        // Prepare the data for the new reply
		$replyData = [
			'reply' => $request->reply,
			'attachment' => $attachment,
		];

		// Determine if the reply is by support based on the route and user role
		if(Str::contains($request->route()->uri, 'admin')){
			$replyData['support_agent_id'] = auth()->user()->isSuperAdmin() ? auth()->id() : null;
		}

		// Create the reply for the ticket
		$reply = $ticket->replies()->create($replyData);

		return $reply;
	}

	/**
     * Retrieve all replies for a specific ticket and format their details.
     *
     * @param  \App\Models\Ticket  $ticket  The ticket for which to retrieve replies.
     * @param  \App\Models\User  $user  The user to format dates for.
     * @return \App\Models\Ticket
     */
   	public function replyIndex($ticket, $user)
   	{
		// Fetch the ticket with its replies
		$ticket = Ticket::where('id', $ticket->id)
			->select('id', 'title')
			->with('replies:id,ticket_id,reply,attachment,support_agent_id,created_at', 'replies.supportAgent:id,name,email,avatar')
			->first();

		// Format the attachment URL and created_at for each reply
		if ($ticket && $ticket->replies) {
            $ticket->replies = $ticket->replies->map(function ($reply) use ($user) {
                $reply->attachment = $reply->attachment ? asset('storage/ticket/' . $reply->attachment) : null;
                $reply->created_at_human = Carbon::parse($reply->created_at)->diffForHumans(now($user->timezone)->toDateTimeString(),true).' ago';
                return $reply;
            });
        }

		return $ticket;
	}

	/**
     * Update the status of a specific ticket and log the activity.
     *
     * @param  \App\Models\Ticket  $ticket  The ticket to update.
     * @param  string  $action  The new status for the ticket.
     * @return array
     */
   	public function statusUpdate($ticket, $action)
   	{
		// Update the ticket's status
        $ticket->update(['status' => $action]);

        return [
        	"message" => "Ticket $ticket->status successfully.",
        ];
	}
}