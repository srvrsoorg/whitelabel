<?php

namespace App\Mail\User\CreditReminder;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Carbon\Carbon;

class ServerDeletionMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    protected $serverData;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $serverData)
    {
        $this->user = $user;
        $this->serverData = $serverData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Server Deletion Due to Unpaid Bill',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
       $endDate = today()->format('Y-m-d');

        return new Content(
            markdown: 'emails.user.creditReminder.serverDeletionMail',
            with: [
                'user' => $this->user,
                'serverData' => $this->serverData,
                'endDate' => $endDate,
            ]
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
