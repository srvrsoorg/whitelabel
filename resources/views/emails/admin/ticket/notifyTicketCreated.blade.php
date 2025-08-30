@component('mail::message')

# Hello {{ $adminUser->name }},

A new support ticket has been created by user and requires your attention.:

## Ticket Details:
- **Ticket ID:** {{ $ticket->id }}
- **Title:** {{ $ticket->title }}
- **Department:** {{ ucfirst($ticket->department) }}
- **Description:** {{ $ticket->replies->first()->reply ?? 'N/A' }}

Please log in to the admin panel to review.

@component('mail::button', ['url' => url('/admin/tickets', $ticket->id)])
View Ticket
@endcomponent

@endcomponent