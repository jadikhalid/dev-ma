<?php

namespace App\Mail;

use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Newsletter $newsletter,
        public NewsletterSubscriber $subscriber,
        public bool $isTest = false,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->newsletter->subject;

        if ($this->isTest) {
            $subject = '[TEST] '.$subject;
        }

        return new Envelope(
            from: MailSender::from(),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $html = app(NewsletterRenderer::class)->renderHtml($this->newsletter, $this->subscriber);

        return new Content(
            htmlString: $html,
        );
    }
}
