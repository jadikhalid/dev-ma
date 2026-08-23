<?php

namespace Tests\Feature;

use App\Mail\TalentProfileCompletionReminderMail;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TalentProfileCompletionReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_reminder_is_sent_when_profile_is_incomplete_after_delay(): void
    {
        Mail::fake();

        $talent = $this->createIncompleteTalentApprovedDaysAgo(3);

        $this->artisan('talents:send-profile-completion-reminders')
            ->assertSuccessful();

        Mail::assertSent(TalentProfileCompletionReminderMail::class, function (TalentProfileCompletionReminderMail $mail) use ($talent) {
            return $mail->hasTo($talent->email);
        });

        $this->assertNotNull($talent->fresh()->profile_completion_reminder_sent_at);
    }

    public function test_reminder_is_not_sent_when_profile_is_catalog_ready(): void
    {
        Mail::fake();

        $talent = $this->createCatalogReadyTalentApprovedDaysAgo(3);

        $this->artisan('talents:send-profile-completion-reminders')
            ->assertSuccessful();

        Mail::assertNothingSent();
        $this->assertNull($talent->fresh()->profile_completion_reminder_sent_at);
    }

    public function test_reminder_is_not_sent_before_delay(): void
    {
        Mail::fake();

        $talent = User::factory()->talent()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now()->subHours(12),
        ]);

        Profile::factory()->create([
            'user_id' => $talent->id,
            'profession_sector_id' => null,
            'profession_id' => null,
        ]);

        $this->artisan('talents:send-profile-completion-reminders')
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    public function test_reminder_is_sent_only_once(): void
    {
        Mail::fake();

        $talent = $this->createIncompleteTalentApprovedDaysAgo(3);

        $this->artisan('talents:send-profile-completion-reminders')->assertSuccessful();
        $this->artisan('talents:send-profile-completion-reminders')->assertSuccessful();

        Mail::assertSent(TalentProfileCompletionReminderMail::class, 1);
        $this->assertNotNull($talent->fresh()->profile_completion_reminder_sent_at);
    }

    public function test_reminder_email_links_to_talent_profile_panel(): void
    {
        $talent = User::factory()->talent()->create([
            'first_name' => 'Karim',
            'last_name' => 'Benali',
            'name' => 'Karim Benali',
        ]);

        $mail = new TalentProfileCompletionReminderMail($talent);
        $html = $mail->render();

        $this->assertStringContainsString(route('profile.edit', ['panel' => 'talent']), $html);
        $this->assertStringContainsString('Talents du Maroc', $html);
        $this->assertStringNotContainsString('Laravel', $html);
    }

    private function createIncompleteTalentApprovedDaysAgo(int $days): User
    {
        $talent = User::factory()->talent()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now()->subDays($days),
        ]);

        Profile::factory()->create([
            'user_id' => $talent->id,
            'profession_sector_id' => null,
            'profession_id' => null,
            'specialization' => null,
            'bio' => null,
        ]);

        return $talent;
    }

    private function createCatalogReadyTalentApprovedDaysAgo(int $days): User
    {
        $sector = ProfessionSector::query()->create([
            'slug' => 'it',
            'name_fr' => 'Technologies de l\'information',
            'name_en' => 'Information technology',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $profession = Profession::query()->create([
            'profession_sector_id' => $sector->id,
            'slug' => 'dev-fullstack',
            'name_fr' => 'Développeur full stack',
            'name_en' => 'Full stack developer',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $talent = User::factory()->talent()->create([
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now()->subDays($days),
        ]);

        Profile::factory()->create([
            'user_id' => $talent->id,
            'profession_sector_id' => $sector->id,
            'profession_id' => $profession->id,
            'specialization' => 'API REST',
            'bio' => str_repeat('a', 40),
            'experience_years' => 3,
            'education_level' => 'bac+5',
            'languages' => ['fr'],
            'availability' => 'disponible',
            'work_modes' => ['remote'],
        ]);

        return $talent;
    }
}
