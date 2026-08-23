<?php

namespace App\Console\Commands;

use App\Services\TalentProfileCompletionReminderService;
use Illuminate\Console\Command;

class SendTalentProfileCompletionRemindersCommand extends Command
{
    protected $signature = 'talents:send-profile-completion-reminders';

    protected $description = 'Envoie une relance aux talents validés dont le profil minimum n\'est pas complet après le délai configuré';

    public function handle(TalentProfileCompletionReminderService $service): int
    {
        $result = $service->sendDueReminders();

        if ($result['sent'] > 0) {
            $this->info("{$result['sent']} relance(s) envoyée(s).");
        }

        if ($result['skipped_complete'] > 0) {
            $this->line("{$result['skipped_complete']} talent(s) ignoré(s) — profil déjà prêt pour le catalogue.");
        }

        if ($result['sent'] === 0 && $result['eligible'] === 0) {
            $this->line('Aucun talent éligible pour une relance.');
        }

        return self::SUCCESS;
    }
}
