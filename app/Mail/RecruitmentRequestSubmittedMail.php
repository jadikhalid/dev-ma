<?php

namespace App\Mail;

use App\Models\RecruitmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecruitmentRequestSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RecruitmentRequest $recruitment,
    ) {}

    public function envelope(): Envelope
    {
        $orgName = $this->recruitment->companyDisplayName();

        return new Envelope(
            from: MailSender::from(),
            subject: __('talenma.mail.recruitment_submitted.subject_'.$this->recruitment->mode, [
                'company' => $orgName,
                'title' => $this->recruitment->subject,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recruitment-submitted',
            with: [
                'recruitment' => $this->recruitment,
                'companyName' => $this->recruitment->companyPersonDisplayName(),
                'talentName' => $this->recruitment->talent?->name,
                'url' => route('admin.recruitment.show', $this->recruitment),
            ],
        );
    }
}
