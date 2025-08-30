<?php

namespace App\Mail\Admin\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\{User, Ticket};

class NotifyTicketCreated extends Mailable
{
    use Queueable, SerializesModels;

    protected $adminUser;
    protected $ticket;
    /**
     * Create a new message instance.
     */
    public function __construct(User $adminUser, Ticket $ticket)
    {
        $this->adminUser = $adminUser;
        $this->ticket = $ticket;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Ticket Created on - ' . config('app.title'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.ticket.notifyTicketCreated',
            with: [
                'adminUser'  => $this->adminUser,
                'ticket' => $this->ticket,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
