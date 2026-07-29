<?php

namespace App\Mail;

use App\Models\RecruitmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecruitmentRequestStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RecruitmentRequest $recruitment,
        public string $status,
        public bool $commentOnly = false,
    ) {}

    public function envelope(): Envelope
    {
        $mode = $this->recruitment->mode === RecruitmentRequest::MODE_NAMED ? 'named' : 'open';
        $key = $this->commentOnly
            ? 'subject_comment'
            : 'subject_'.$this->normalizedStatus().'_'.$mode;

        return new Envelope(
            from: new Address(
                (string) config('mail.from.address'),
                'Talents du Maroc',
            ),
            subject: __('talenma.mail.recruitment_status.'.$key, [
                'title' => $this->recruitment->subject,
            ]),
        );
    }

    public function content(): Content
    {
        $mode = $this->recruitment->mode === RecruitmentRequest::MODE_NAMED ? 'named' : 'open';
        $bodyKey = $this->commentOnly
            ? 'body_comment_'.$mode
            : 'body_'.$this->normalizedStatus().'_'.$mode;

        return new Content(
            view: 'emails.recruitment-status',
            with: [
                'greetingName' => $this->recruitment->companyPersonDisplayName(),
                'body' => __('talenma.mail.recruitment_status.'.$bodyKey, [
                    'title' => $this->recruitment->subject,
                ]),
                'comment' => $this->recruitment->admin_comment,
                'url' => route('sourcing.show', $this->recruitment),
            ],
        );
    }

    private function normalizedStatus(): string
    {
        return $this->recruitment->normalizeStatus($this->status);
    }
}
