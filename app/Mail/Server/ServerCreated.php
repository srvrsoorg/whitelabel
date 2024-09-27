<?php

namespace App\Mail\Server;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Server\Server;

class ServerCreated extends Mailable
{
    use Queueable, SerializesModels;

    protected $server;
    /**
     * Create a new message instance.
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Server Has Successfully Created',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.server.serverCreated',
            with: [
                'server' => $this->server,
                'user' => $this->server->user
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
