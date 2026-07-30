<?php

namespace App\Mail;

use App\Models\DirectHireMessage;
use App\Models\DirectHireRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DirectHireChatMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DirectHireRequest $directHire,
        public DirectHireMessage $chatMessage,
        public User $sender,
        public User $recipient,
        public bool $recipientIsCompany,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: MailSender::from(),
            subject: __('talenma.mail.direct_hire_chat.subject', [
                'name' => $this->senderDisplayName(),
            ]),
        );
    }

    public function content(): Content
    {
        $url = $this->recipientIsCompany
            ? route('company.direct-hire.show', $this->directHire)
            : route('talent.direct-hire.show', $this->directHire);

        return new Content(
            view: 'emails.direct-hire-chat',
            with: [
                'greetingName' => $this->recipientGreetingName(),
                'senderName' => $this->senderDisplayName(),
                'subject' => $this->directHire->shortSubject(),
                'preview' => \Illuminate\Support\Str::limit($this->chatMessage->body, 220),
                'url' => $url,
            ],
        );
    }

    private function senderDisplayName(): string
    {
        if ($this->sender->isCompany()) {
            return $this->directHire->companyRecipientGreetingName();
        }

        return $this->directHire->talentFormalDisplayName();
    }

    private function recipientGreetingName(): string
    {
        if ($this->recipientIsCompany) {
            return $this->directHire->companyRecipientGreetingName();
        }

        return $this->directHire->talentFormalDisplayName();
    }
}
