<?php

namespace App\Mail;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use Illuminate\Contracts\Queue\ShouldQueue;

class SupportTicketMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticket;
    public $messageObj;
    public $isReply;

    /**
     * Create a new message instance.
     *
     * @param SupportTicket $ticket
     * @param SupportMessage|null $messageObj
     * @param bool $isReply
     */
    public function __construct(SupportTicket $ticket, SupportMessage $messageObj = null, bool $isReply = false)
    {
        $this->ticket = $ticket;
        $this->messageObj = $messageObj;
        $this->isReply = $isReply;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isReply 
            ? 'Re: Soporte ParkiApp - ' . $this->ticket->subject 
            : 'Nuevo Ticket de Soporte [' . $this->ticket->id . ']: ' . $this->ticket->subject;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.support_ticket',
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
