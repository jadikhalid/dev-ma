<?php

namespace App\Mail;

use App\Models\RecruitmentRequest;
use App\Models\RecruitmentRequestMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class RecruitmentRequestChatMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RecruitmentRequest $recruitment,
        public RecruitmentRequestMessage $chatMessage,
        public User $sender,
        public User $recipient,
        public bool $recipientIsCompany,
    ) {}

    public function envelope(): Envelope
    {
        $senderName = $this->senderDisplayName();

        return new Envelope(
            from: new Address(
                (string) config('mail.from.address'),
                $this->recipientIsCompany
                    ? 'Talents du Maroc'
                    : 'Talents du Maroc / '.$this->recruitment->companyDisplayName(),
            ),
            subject: __('talenma.mail.recruitment_chat.subject', [
                'name' => $senderName,
            ]),
        );
    }

    public function content(): Content
    {
        $url = $this->recipientIsCompany
            ? route('sourcing.show', $this->recruitment)
            : route('admin.recruitment.show', $this->recruitment);

        return new Content(
            view: 'emails.recruitment-chat',
            with: [
                'greetingName' => $this->recipientIsCompany
                    ? $this->recruitment->companyPersonDisplayName()
                    : $this->recipient->name,
                'senderName' => $this->senderDisplayName(),
                'subject' => $this->recruitment->subject,
                'preview' => Str::limit($this->chatMessage->body, 220),
                'url' => $url.'#sourcing-chat',
            ],
        );
    }

    private function senderDisplayName(): string
    {
        if ($this->sender->isStaff()) {
            return __('talenma.recruitment.chat_peer_team');
        }

        return $this->recruitment->companyPersonDisplayName();
    }
}
