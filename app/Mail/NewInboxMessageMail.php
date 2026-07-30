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
            from: MailSender::from(),
            subject: __('talenma.mail.inbox_message.subject', [
                'name' => $this->senderCompanyName(),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inbox-message',
            with: [
                'recipient' => $this->recipient,
                'greetingName' => $this->recipientGreetingName(),
                'senderName' => $this->senderBodyLabel(),
                'subject' => $this->conversation->subject,
                'preview' => \Illuminate\Support\Str::limit($this->message->body, 180),
                'inboxUrl' => route('inbox.show', $this->conversation),
            ],
        );
    }

    private function recipientGreetingName(): string
    {
        if ($this->recipient->isCompany()) {
            return $this->recipient->companyMailPersonName();
        }

        return $this->recipient->name;
    }

    private function senderCompanyName(): string
    {
        if ($this->sender->isCompany()) {
            $org = $this->sender->companyOrganization()?->displayName();

            if (filled($org)) {
                return $org;
            }

            return $this->sender->companyDisplayName();
        }

        return $this->sender->name;
    }

    private function senderPersonName(): string
    {
        if ($this->sender->isCompany()) {
            return $this->sender->companyMailPersonName();
        }

        return $this->sender->name;
    }

    private function senderBodyLabel(): string
    {
        if (! $this->sender->isCompany()) {
            return $this->sender->name;
        }

        $company = $this->senderCompanyName();
        $person = $this->senderPersonName();

        if ($person !== '' && strcasecmp($person, $company) !== 0) {
            return __('talenma.mail.inbox_message.sender_with_company', [
                'person' => $person,
                'company' => $company,
            ]);
        }

        return $company;
    }
}
