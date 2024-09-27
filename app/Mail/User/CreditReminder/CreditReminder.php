<?php

namespace App\Mail\User\CreditReminder;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Server\Subscription;
use App\Models\User;

class CreditReminder extends Mailable
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
            subject: "Outstanding Balance Reminder for Your " . config('app.title') . " Account",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Fetch reminders where end_at is today
        $creditReminders = $this->user->creditReminders()
            ->whereHas('server')
            ->get();
        
        return new Content(
            markdown: 'emails.user.creditReminder.creditReminder',
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
