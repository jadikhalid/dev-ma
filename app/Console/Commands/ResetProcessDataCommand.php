<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ResetProcessDataCommand extends Command
{
    protected $signature = 'processes:reset {--force : Ne pas demander de confirmation}';

    protected $description = 'Vide les données des processus (recrutement direct, annonces, sourcing, messagerie) sans toucher aux comptes';

    /**
     * Children before parents so the wipe works even with foreign keys enabled.
     *
     * @var list<string>
     */
    private const TABLES = [
        'message_attachments',
        'messages',
        'direct_hire_status_events',
        'direct_hire_messages',
        'direct_hire_rounds',
        'job_applications',
        'job_posting_activity_events',
        'recruitment_request_messages',
        'recruitment_request_status_events',
        'direct_hire_requests',
        'conversations',
        'job_postings',
        'recruitment_requests',
    ];

    public function handle(): int
    {
        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('Refusé en production. Relancez avec --force si c\'est volontaire.');

            return self::FAILURE;
        }

        $database = DB::connection()->getDatabaseName();

        if (! $this->option('force') && ! $this->confirm("Vider les processus sur « {$database} » ? Action irréversible.")) {
            $this->comment('Annulé.');

            return self::SUCCESS;
        }

        $deleted = [];

        Schema::disableForeignKeyConstraints();

        try {
            foreach (self::TABLES as $table) {
                if (! Schema::hasTable($table)) {
                    continue;
                }

                $deleted[$table] = DB::table($table)->count();
                DB::table($table)->truncate();
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $seenReset = DB::table('users')
            ->whereNotNull('dashboard_activity_seen_at')
            ->update(['dashboard_activity_seen_at' => null]);

        $files = $this->purgeMessageAttachments();

        $this->table(
            ['Table', 'Lignes supprimées'],
            collect($deleted)->map(fn (int $count, string $table) => [$table, $count])->values()->all(),
        );

        $this->info("Total : {$this->sum($deleted)} ligne(s) supprimée(s) sur « {$database} ».");
        $this->info("Utilisateurs réinitialisés (activité vue) : {$seenReset}.");
        $this->info("Pièces jointes supprimées : {$files}.");

        return self::SUCCESS;
    }

    private function purgeMessageAttachments(): int
    {
        $disk = Storage::disk('local');

        if (! $disk->exists('message-attachments')) {
            return 0;
        }

        $files = count($disk->allFiles('message-attachments'));
        $disk->deleteDirectory('message-attachments');

        return $files;
    }

    /**
     * @param  array<string, int>  $deleted
     */
    private function sum(array $deleted): int
    {
        return array_sum($deleted);
    }
}
