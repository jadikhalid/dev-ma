<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\UserDeletionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class PurgeNonStaffUsersCommand extends Command
{
    protected $signature = 'users:purge-non-staff {--force : Ne pas demander de confirmation}';

    protected $description = 'Supprime tous les comptes talents et entreprises (assets et dossiers inclus), en conservant le staff';

    public function handle(UserDeletionService $deletions): int
    {
        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('Refusé en production. Relancez avec --force si c\'est volontaire.');

            return self::FAILURE;
        }

        $database = DB::connection()->getDatabaseName();
        $total = $this->targets()->count();

        if ($total === 0) {
            $this->info('Aucun compte à supprimer.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Supprimer {$total} compte(s) sur « {$database} » ? Action irréversible.")) {
            $this->comment('Annulé.');

            return self::SUCCESS;
        }

        $deleted = 0;
        $failures = [];
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // Members first: removing an owner cascades its memberships away.
        $this->targets()
            ->orderByRaw("company_seat = 'owner'")
            ->chunkById(50, function ($users) use ($deletions, &$deleted, &$failures, $bar) {
                foreach ($users as $user) {
                    try {
                        $deletions->delete($user);
                        $deleted++;
                    } catch (Throwable $e) {
                        $failures[] = "#{$user->id} {$user->email} : {$e->getMessage()}";
                    }

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);

        $this->info("{$deleted} compte(s) supprimé(s) sur « {$database} ».");

        if ($failures !== []) {
            $this->error(count($failures).' échec(s) :');

            foreach ($failures as $failure) {
                $this->line('  - '.$failure);
            }

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<User>
     */
    private function targets()
    {
        return User::query()
            ->where('role', '!=', 'admin')
            ->whereDoesntHave('moderatorAssignments', fn ($query) => $query->whereNull('revoked_at'));
    }
}
