<?php

namespace Tests\Feature\Admin;

use App\Models\PendingRegistration;
use App\Models\PlatformSetting;
use App\Models\User;
use Database\Seeders\ProfessionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TalentAdminValidationSettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ProfessionSeeder::class);
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'approval_status' => null,
        ]);
    }

    public function test_talent_is_auto_approved_when_validation_is_disabled(): void
    {
        Mail::fake();
        PlatformSetting::setRequiresTalentAdminValidation(false);

        $this->post('/register', [
            'first_name' => 'Auto',
            'last_name' => 'Talent',
            'email' => 'auto-talent@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'role' => 'dev',
            'data_processing_consent' => '1',
        ])->assertRedirect();

        $pending = PendingRegistration::query()->where('email', 'auto-talent@example.com')->firstOrFail();
        $response = $this->get(route('register.verify', ['token' => $pending->token]));

        $response->assertRedirect(route('profile.edit', ['panel' => 'talent']));
        $response->assertSessionHas('toast_sticky_success', __('talenma.auth.registration_welcome_complete_profile'));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'auto-talent@example.com')->firstOrFail();
        $this->assertSame(User::APPROVAL_APPROVED, $user->approval_status);
        $this->assertNotNull($user->approved_at);
    }

    public function test_company_stays_pending_when_talent_validation_is_disabled(): void
    {
        Mail::fake();
        PlatformSetting::setRequiresTalentAdminValidation(false);

        $this->post('/register', [
            'name' => 'Acme SAS',
            'email' => 'company-auto@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'role' => 'company',
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile, à la recherche de talents pour accompagner notre croissance.',
            'company_country' => 'fr',
            'data_processing_consent' => '1',
            'documents' => [
                UploadedFile::fake()->create('kbis.pdf', 100, 'application/pdf'),
            ],
        ])->assertRedirect(route('login'));

        $pending = PendingRegistration::query()->where('email', 'company-auto@example.com')->firstOrFail();
        $response = $this->get(route('register.verify', ['token' => $pending->token]));

        $response->assertRedirect(route('account.pending'));

        $user = User::query()->where('email', 'company-auto@example.com')->firstOrFail();
        $this->assertSame(User::APPROVAL_PENDING, $user->approval_status);
        $this->assertNull($user->approved_at);
    }

    public function test_talent_verification_email_omits_review_when_validation_disabled(): void
    {
        Mail::fake();
        PlatformSetting::setRequiresTalentAdminValidation(false);

        $this->post('/register', [
            'first_name' => 'Auto',
            'last_name' => 'Talent',
            'email' => 'auto-talent-mail@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'role' => 'dev',
            'data_processing_consent' => '1',
        ])->assertRedirect();

        Mail::assertSent(\App\Mail\VerifyRegistrationMail::class, function ($mail) {
            $html = $mail->render();

            return str_contains($html, __('talenma.mail.verify_registration.body'))
                && ! str_contains($html, __('talenma.mail.verify_registration.body_review'));
        });
    }

    public function test_talent_verification_email_includes_review_when_validation_enabled(): void
    {
        Mail::fake();
        PlatformSetting::setRequiresTalentAdminValidation(true);

        $this->post('/register', [
            'first_name' => 'Pending',
            'last_name' => 'Talent',
            'email' => 'pending-talent-mail@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'role' => 'dev',
            'data_processing_consent' => '1',
        ])->assertRedirect();

        Mail::assertSent(\App\Mail\VerifyRegistrationMail::class, function ($mail) {
            $html = $mail->render();

            return str_contains($html, __('talenma.mail.verify_registration.body_review'));
        });
    }

    public function test_company_verification_email_always_includes_review(): void
    {
        Mail::fake();
        PlatformSetting::setRequiresTalentAdminValidation(false);

        $this->post('/register', [
            'name' => 'Acme SAS',
            'email' => 'company-review-mail@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'role' => 'company',
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile, à la recherche de talents pour accompagner notre croissance.',
            'company_country' => 'fr',
            'data_processing_consent' => '1',
        ])->assertRedirect(route('login'));

        Mail::assertSent(\App\Mail\VerifyRegistrationMail::class, function ($mail) {
            $html = $mail->render();

            return str_contains($html, __('talenma.mail.verify_registration.body_review'));
        });
    }

    public function test_admin_can_toggle_talent_validation_setting(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.settings.talent-validation'), [
                'require_talent_admin_validation' => '0',
            ])
            ->assertRedirect();

        $this->assertFalse(PlatformSetting::requiresTalentAdminValidation());

        $this->actingAs($admin)
            ->put(route('admin.settings.talent-validation'), [
                'require_talent_admin_validation' => '1',
            ])
            ->assertRedirect();

        $this->assertTrue(PlatformSetting::requiresTalentAdminValidation());
    }

    public function test_non_admin_cannot_toggle_talent_validation_setting(): void
    {
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_APPROVED,
        ]);

        $this->actingAs($talent)
            ->put(route('admin.settings.talent-validation'), [
                'require_talent_admin_validation' => '0',
            ])
            ->assertForbidden();

        $this->assertTrue(PlatformSetting::requiresTalentAdminValidation());
    }

    public function test_talent_registers_immediately_when_email_verification_is_disabled(): void
    {
        Mail::fake();
        PlatformSetting::setRequiresTalentEmailVerification(false);
        PlatformSetting::setRequiresTalentAdminValidation(false);

        $response = $this->post('/register', [
            'first_name' => 'Direct',
            'last_name' => 'Talent',
            'email' => 'direct-talent@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'role' => 'dev',
            'data_processing_consent' => '1',
        ]);

        $user = User::query()->where('email', 'direct-talent@example.com')->firstOrFail();
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame(User::APPROVAL_APPROVED, $user->approval_status);
        $this->assertNull($user->profile?->bio);
        $this->assertDatabaseMissing('pending_registrations', ['email' => 'direct-talent@example.com']);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('profile.edit', ['panel' => 'talent']));
        $response->assertSessionHas('toast_sticky_success', __('talenma.auth.registration_welcome_complete_profile'));

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee(__('talenma.auth.registration_welcome_complete_profile'), false);

        Mail::assertNothingSent();
    }

    public function test_company_still_requires_email_when_talent_email_verification_is_disabled(): void
    {
        Mail::fake();
        PlatformSetting::setRequiresTalentEmailVerification(false);

        $this->post('/register', [
            'name' => 'Acme SAS',
            'email' => 'company-email-required@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'role' => 'company',
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile, à la recherche de talents pour accompagner notre croissance.',
            'company_country' => 'fr',
            'data_processing_consent' => '1',
            'documents' => [
                UploadedFile::fake()->create('kbis.pdf', 100, 'application/pdf'),
            ],
        ])->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'company-email-required@example.com']);
        $this->assertDatabaseHas('pending_registrations', ['email' => 'company-email-required@example.com']);
        Mail::assertSent(\App\Mail\VerifyRegistrationMail::class);
    }

    public function test_admin_can_toggle_talent_email_verification_setting(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.settings.talent-email-verification'), [
                'require_talent_email_verification' => '0',
            ])
            ->assertRedirect();

        $this->assertFalse(PlatformSetting::requiresTalentEmailVerification());

        $this->actingAs($admin)
            ->put(route('admin.settings.talent-email-verification'), [
                'require_talent_email_verification' => '1',
            ])
            ->assertRedirect();

        $this->assertTrue(PlatformSetting::requiresTalentEmailVerification());
    }

    public function test_non_admin_cannot_toggle_talent_email_verification_setting(): void
    {
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_APPROVED,
        ]);

        $this->actingAs($talent)
            ->put(route('admin.settings.talent-email-verification'), [
                'require_talent_email_verification' => '0',
            ])
            ->assertForbidden();

        $this->assertTrue(PlatformSetting::requiresTalentEmailVerification());
    }
}
