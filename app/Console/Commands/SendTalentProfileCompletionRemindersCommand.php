<?php

namespace App\Console\Commands;

use App\Services\TalentProfileCompletionReminderService;
use Illuminate\Console\Command;

class SendTalentProfileCompletionRemindersCommand extends Command
{
    protected $signature = 'talents:send-profile-completion-reminders';

    protected $description = 'Envoie jusqu\'à 3 relances CV aux talents validés sans CV, espacées du délai configuré';

    public function handle(TalentProfileCompletionReminderService $service): int
    {
        $result = $service->sendDueReminders();

        if ($result['sent'] > 0) {
            $this->info("{$result['sent']} relance(s) envoyée(s).");
        }

        if ($result['sent'] === 0 && $result['eligible'] === 0) {
            $this->line('Aucun talent éligible pour une relance.');
        }

        return self::SUCCESS;
    }
}
