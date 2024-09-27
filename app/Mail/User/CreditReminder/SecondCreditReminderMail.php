<?php

namespace App\Mail\User\CreditReminder;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SecondCreditReminderMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Action Required: Prevent Deletion of Your Servers Due to Unpaid Bill",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $creditReminders = $this->user->creditReminders()
            ->whereHas('server')
            ->get();

        return new Content(
            markdown: 'emails.user.creditReminder.SecondCreditReminderMail',
            with: [
                "user" => $this->user,
                "creditReminders" => $creditReminders,
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
