<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TalentProfileCompletionReminderMail extends PortalMailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public int $step = 1,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: MailSender::from(),
            subject: (string) data_get($this->copy(), 'subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.talent-profile-completion-reminder',
            with: [
                'copy' => $this->copy(),
                'step' => $this->step,
                'isLast' => $this->step >= (int) config('talenma.profile_completion_reminder.max_reminders', 3),
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function copy(): array
    {
        $copy = __('talenma.mail.talent_profile_completion_reminder.'.$this->step);

        return is_array($copy) ? $copy : [];
    }
}
