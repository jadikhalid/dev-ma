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
        $url = $this->showUrl();

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

    private function showUrl(): string
    {
        if (! $this->recipientIsCompany) {
            return route('talent.direct-hire.show', $this->directHire);
        }

        if ($this->recipient->isStaff()) {
            return route('admin.direct-hire.show', $this->directHire);
        }

        return route('company.direct-hire.show', $this->directHire);
    }

    private function senderDisplayName(): string
    {
        if ($this->sender->isTalent()) {
            return $this->directHire->talentFormalDisplayName();
        }

        if (! $this->recipientIsCompany) {
            return $this->directHire->talentFacingCompanyName();
        }

        if ($this->sender->isStaff()) {
            return $this->sender->name ?: __('talenma.direct_hire.platform_employer_name');
        }

        return $this->directHire->companyRecipientGreetingName();
    }

    private function recipientGreetingName(): string
    {
        if ($this->recipientIsCompany) {
            if ($this->recipient->isStaff()) {
                return $this->recipient->name ?: __('talenma.direct_hire.platform_employer_name');
            }

            return $this->directHire->companyRecipientGreetingName();
        }

        return $this->directHire->talentFormalDisplayName();
    }
}
