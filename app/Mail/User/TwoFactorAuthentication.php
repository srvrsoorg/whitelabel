<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\User\TwoFa;

class TwoFactorAuthentication extends Mailable
{
    use Queueable, SerializesModels;

    public $user, $twoFa, $ip, $userAgent;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, TwoFa $twoFa, $ip, $userAgent)
    {
        $this->user = $user;
        $this->twoFa = $twoFa;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Two Factor Authentication',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.user.twoFactorAuthentication',
            with:[
                'user'=>$this->user,
                'twoFa'=>$this->twoFa,
                'ip'=>$this->ip,
                'userAgent'=>$this->userAgent
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
