<?php

namespace App\Mail;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewInboxMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public User $sender,
        public Conversation $conversation,
        public Message $message,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('talenma.mail.inbox_message.subject', [
                'name' => $this->senderDisplayName(),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inbox-message',
            with: [
                'recipient' => $this->recipient,
                'senderName' => $this->senderDisplayName(),
                'subject' => $this->conversation->subject,
                'preview' => \Illuminate\Support\Str::limit($this->message->body, 180),
                'inboxUrl' => route('inbox.show', $this->conversation),
            ],
        );
    }

    private function senderDisplayName(): string
    {
        if ($this->sender->isCompany()) {
            $this->sender->loadMissing('companyProfile');

            return $this->sender->name;
        }

        return $this->sender->name;
    }
}
