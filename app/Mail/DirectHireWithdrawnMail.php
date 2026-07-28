<?php

namespace App\Mail;

use App\Models\DirectHireRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DirectHireWithdrawnMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DirectHireRequest $directHire) {}

    public function envelope(): Envelope
    {
        $companyName = $this->directHire->companyFormalDisplayName();

        return new Envelope(
            from: new Address(
                (string) config('mail.from.address'),
                $this->directHire->mailFromNameAsCompany(),
            ),
            subject: __('talenma.mail.direct_hire_withdrawn.subject', [
                'company' => $companyName,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.direct-hire-withdrawn',
            with: [
                'directHire' => $this->directHire,
                'talentName' => $this->directHire->talentFormalDisplayName(),
                'companyName' => $this->directHire->companyFormalDisplayName(),
                'url' => route('talent.direct-hire.show', $this->directHire),
            ],
        );
    }
}
