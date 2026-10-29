<?php

namespace Tests\Feature;

use App\Mail\TalentProfileCompletionReminderMail;
use App\Models\Profession;
use App\Models\ProfessionSector;
use App\Models\Profile;
use App\Models\ProfileDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TalentProfileCompletionReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_reminder_is_sent_when_cv_is_missing_after_delay(): void
    {
        Mail::fake();

        $talent = $this->createIncompleteTalentApprovedDaysAgo(3);

        $this->artisan('talents:send-profile-completion-reminders')
            ->assertSuccessful();

        Mail::assertSent(TalentProfileCompletionReminderMail::class, function (TalentProfileCompletionReminderMail $mail) use ($talent) {
            return $mail->hasTo($talent->email) && $mail->step === 1;
        });

        $fresh = $talent->fresh();
        $this->assertNotNull($fresh->profile_completion_reminder_sent_at);
        $this->assertSame(1, (int) $fresh->profile_completion_reminder_count);
    }

    public function test_reminder_is_not_sent_when_cv_is_present(): void
    {
        Mail::fake();

        $talent = $this->createIncompleteTalentApprovedDaysAgo(3);
        $this->attachCv($talent);

        $this->artisan('talents:send-profile-completion-reminders')
            ->assertSuccessful();

        Mail::assertNothingSent();
        $this->assertSame(0, (int) $talent->fresh()->profile_completion_reminder_count);
    }

    public function test_reminder_is_sent_when_only_the_cv_is_missing(): void
    {
        Mail::fake();

        $talent = $this->createCatalogReadyTalentApprovedDaysAgo(3);

        $this->artisan('talents:send-profile-completion-reminders')
            ->assertSuccessful();

        Mail::assertSent(TalentProfileCompletionReminderMail::class, function (TalentProfileCompletionReminderMail $mail) use ($talent) {
            return $mail->hasTo($talent->email) && $mail->step === 1;
        });
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

    public function test_second_and_third_reminders_are_sent_after_each_interval(): void
    {
        Mail::fake();

        $talent = $this->createIncompleteTalentApprovedDaysAgo(3);

        $this->artisan('talents:send-profile-completion-reminders')->assertSuccessful();
        Mail::assertSent(TalentProfileCompletionReminderMail::class, 1);
        $this->assertSame(1, (int) $talent->fresh()->profile_completion_reminder_count);

        $this->travel(49)->hours();
        Mail::fake();
        $this->artisan('talents:send-profile-completion-reminders')->assertSuccessful();
        Mail::assertSent(TalentProfileCompletionReminderMail::class, fn (TalentProfileCompletionReminderMail $mail) => $mail->hasTo($talent->email) && $mail->step === 2);
        $this->assertSame(2, (int) $talent->fresh()->profile_completion_reminder_count);

        $this->travel(49)->hours();
        Mail::fake();
        $this->artisan('talents:send-profile-completion-reminders')->assertSuccessful();
        Mail::assertSent(TalentProfileCompletionReminderMail::class, fn (TalentProfileCompletionReminderMail $mail) => $mail->hasTo($talent->email) && $mail->step === 3);
        $this->assertSame(3, (int) $talent->fresh()->profile_completion_reminder_count);

        $this->travel(49)->hours();
        Mail::fake();
        $this->artisan('talents:send-profile-completion-reminders')->assertSuccessful();
        Mail::assertNothingSent();
    }

    public function test_reminder_is_not_resent_immediately(): void
    {
        Mail::fake();

        $talent = $this->createIncompleteTalentApprovedDaysAgo(3);

        $this->artisan('talents:send-profile-completion-reminders')->assertSuccessful();
        $this->artisan('talents:send-profile-completion-reminders')->assertSuccessful();

        Mail::assertSent(TalentProfileCompletionReminderMail::class, 1);
        $this->assertSame(1, (int) $talent->fresh()->profile_completion_reminder_count);
    }

    public function test_reminder_email_links_to_talent_profile_panel(): void
    {
        $talent = User::factory()->talent()->create([
            'first_name' => 'Karim',
            'last_name' => 'Benali',
            'name' => 'Karim Benali',
        ]);

        $mail = new TalentProfileCompletionReminderMail($talent, 1);
        $html = $mail->render();

        $this->assertStringContainsString(route('profile.edit', ['panel' => 'talent']), $html);
        $this->assertStringContainsString(e(__('talenma.mail.talent_profile_completion_reminder.1.cta')), $html);
        $this->assertStringContainsString(e(__('talenma.mail.talent_profile_completion_reminder.1.rest')), $html);
        $this->assertStringContainsString(e(__('talenma.mail.talent_profile_completion_reminder.1.steps')[2]), $html);
        $this->assertStringNotContainsString('Laravel', $html);
    }

    public function test_last_reminder_mentions_final_notice(): void
    {
        $talent = User::factory()->talent()->create([
            'first_name' => 'Karim',
            'last_name' => 'Benali',
            'name' => 'Karim Benali',
        ]);

        $html = (new TalentProfileCompletionReminderMail($talent, 3))->render();

        $this->assertStringContainsString(e(__('talenma.mail.talent_profile_completion_reminder.3.banner_kicker')), $html);
        $this->assertStringContainsString(e(__('talenma.mail.talent_profile_completion_reminder.3.banner_title')), $html);
        $this->assertStringContainsString(e(__('talenma.mail.talent_profile_completion_reminder.3.good_news')), $html);
        $this->assertSame(
            __('talenma.mail.talent_profile_completion_reminder.3.subject'),
            (new TalentProfileCompletionReminderMail($talent, 3))->envelope()->subject,
        );
    }

    private function attachCv(User $talent): void
    {
        $talent->profile->documents()->create([
            'document_type' => ProfileDocument::TYPE_CV,
            'path' => 'profile-documents/'.$talent->id.'/cv.pdf',
            'original_name' => 'cv.pdf',
            'mime_type' => 'application/pdf',
            'size' => 1000,
            'sort_order' => 1,
        ]);
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
