<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SendCreditReminder extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    protected $availableCredits;
    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $availableCredits)
    {
        $this->user = $user;
        $this->availableCredits = $availableCredits;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reminder: Your " . config('app.title') . " Credits Are Below the Minimum Balance",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user.sendCreditReminder',
            with: [
                'user' => $this->user,
                'availableCredits' => $this->availableCredits
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
