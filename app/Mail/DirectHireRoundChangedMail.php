<?php

namespace App\Mail;

use App\Models\DirectHireRequest;
use App\Models\DirectHireRound;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DirectHireRoundChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  'created'|'updated'  $event
     */
    public function __construct(
        public DirectHireRequest $directHire,
        public DirectHireRound $round,
        public string $event,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('talenma.mail.direct_hire_round_changed.subject_'.$this->event, [
                'title' => $this->round->title,
                'company' => $this->directHire->companyDisplayName(),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.direct-hire-round-changed',
            with: [
                'directHire' => $this->directHire,
                'round' => $this->round,
                'event' => $this->event,
                'talentName' => $this->directHire->talentDisplayName()
                    ?: ($this->directHire->talent?->name ?? ''),
                'companyName' => $this->directHire->companyDisplayName(),
                'url' => route('talent.direct-hire.show', $this->directHire),
            ],
        );
    }
}
