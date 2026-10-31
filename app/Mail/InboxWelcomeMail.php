<?php

namespace App\Mail;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InboxWelcomeMail extends PortalMailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public Conversation $conversation,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: MailSender::from(),
            subject: __('talenma.mail.inbox_welcome.subject'),
        );
    }

    public function content(): Content
    {
        $greetingName = $this->recipient->isCompany()
            ? $this->recipient->companyMailPersonName()
            : $this->recipient->name;

        return new Content(
            view: 'emails.inbox-welcome',
            with: [
                'greetingName' => $greetingName,
                'intro' => __('talenma.mail.inbox_welcome.intro'),
                'tips' => __('talenma.mail.inbox_welcome.tips'),
                'closing' => __('talenma.mail.inbox_welcome.closing'),
                'inboxUrl' => route('inbox.show', $this->conversation),
            ],
        );
    }
}
