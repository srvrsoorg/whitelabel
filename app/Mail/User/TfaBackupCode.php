<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TfaBackupCode extends Mailable
{
    use Queueable, SerializesModels;

    protected $userName, $backupCodes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userName, $backupCodes)
    {
        $this->userName = $userName;
        $this->backupCodes = $backupCodes;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Backup Codes',
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
            markdown: 'emails.user.tfaBackupCode',
            with: [
                'userName' => $this->userName,
                'backupCodes' => $this->backupCodes,
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
