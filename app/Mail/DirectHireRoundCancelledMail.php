<?php

namespace App\Mail;

use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DirectHireRoundCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DirectHireRequest $directHire,
        public DirectHireRound $round,
        public string $recipientRole,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: MailSender::from(),
            subject: __('talenma.mail.direct_hire_round_cancelled.subject', [
                'title' => $this->round->title,
            ]),
        );
    }

    public function content(): Content
    {
        $isTalent = $this->recipientRole === 'talent';

        return new Content(
            view: 'emails.direct-hire-round-cancelled',
            with: [
                'directHire' => $this->directHire,
                'round' => $this->round,
                'greetingName' => $isTalent
                    ? $this->directHire->talentFormalDisplayName()
                    : $this->directHire->companyRecipientGreetingName(),
                'companyName' => $isTalent
                    ? $this->directHire->companyFormalDisplayName()
                    : $this->directHire->companyDisplayName(),
                'url' => $isTalent
                    ? route('talent.direct-hire.show', $this->directHire)
                    : ($this->directHire->isStaffInitiated()
                        ? route('admin.direct-hire.show', $this->directHire)
                        : route('company.direct-hire.show', $this->directHire)),
            ],
        );
    }
}
