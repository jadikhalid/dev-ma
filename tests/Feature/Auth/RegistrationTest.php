<?php

namespace Tests\Feature\Auth;

use App\Mail\VerifyRegistrationMail;
use App\Models\PendingRegistration;
use App\Models\User;
use Database\Seeders\ProfessionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ProfessionSeeder::class);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'role' => 'dev',
        ], $overrides);
    }

    private function validTalentPayload(array $overrides = []): array
    {
        return array_merge($this->validPayload(), [
            'sector' => 'it-digital',
            'description' => 'Développeur passionné avec plus de cinq ans d\'expérience en Laravel et React.',
            'documents' => [
                UploadedFile::fake()->create('diploma.pdf', 100, 'application/pdf'),
            ],
        ], $overrides);
    }

    private function validCompanyPayload(array $overrides = []): array
    {
        return array_merge($this->validPayload(), [
            'role' => 'company',
            'representative_name' => 'Jean Dupont',
            'representative_email' => 'jean.dupont@acme.com',
            'company_need' => 'Nous recherchons un développeur Laravel senior pour une mission de 6 mois en télétravail.',
        ], $overrides);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_registration_stores_pending_until_email_is_verified(): void
    {
        Mail::fake();

        $response = $this->post('/register', $this->validTalentPayload());

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('toast_success');
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
        $this->assertDatabaseHas('pending_registrations', ['email' => 'test@example.com']);

        Mail::assertSent(VerifyRegistrationMail::class, function (VerifyRegistrationMail $mail) {
            return $mail->hasTo('test@example.com')
                && str_contains($mail->verificationUrl, '/register/verify/');
        });
    }

    public function test_email_verification_creates_user_and_profile(): void
    {
        Mail::fake();

        $this->post('/register', $this->validTalentPayload());

        $pending = PendingRegistration::query()->where('email', 'test@example.com')->firstOrFail();

        $response = $this->get(route('register.verify', ['token' => $pending->token]));

        $this->assertAuthenticated();
        $response->assertRedirect(route('account.pending'));

        $user = User::query()->where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotNull($user->profile);
        $this->assertSame('Développeur passionné avec plus de cinq ans d\'expérience en Laravel et React.', $user->profile->registration_description);
        $this->assertCount(1, $user->profile->documents);
        $this->assertSame(User::APPROVAL_PENDING, $user->approval_status);
        $this->assertDatabaseMissing('pending_registrations', ['email' => 'test@example.com']);
    }

    public function test_expired_registration_link_is_rejected_and_purged(): void
    {
        $pending = PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => 'expired@example.com',
            'locale' => 'fr',
            'payload' => [
                'name' => 'Expired User',
                'password' => bcrypt('Password1'),
                'role' => 'dev',
                'sector' => 'it-digital',
                'description' => 'Description suffisamment longue pour validation.',
            ],
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->get(route('register.verify', ['token' => $pending->token]));

        $response->assertRedirect(route('register'));
        $response->assertSessionHas('toast_error');
        $this->assertDatabaseMissing('pending_registrations', ['email' => 'expired@example.com']);
        $this->assertDatabaseMissing('users', ['email' => 'expired@example.com']);
    }

    public function test_unverified_user_cannot_login(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'legacy@example.com',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'legacy@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_company_registration_is_pending_until_email_verification(): void
    {
        Mail::fake();

        $response = $this->post('/register', $this->validCompanyPayload([
            'email' => 'company@example.com',
            'name' => 'Acme SAS',
        ]));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('pending_registrations', ['email' => 'company@example.com']);
        $this->assertDatabaseMissing('users', ['email' => 'company@example.com']);

        $pending = PendingRegistration::query()->where('email', 'company@example.com')->firstOrFail();
        $this->get(route('register.verify', ['token' => $pending->token]));

        $user = User::query()->where('email', 'company@example.com')->first();
        $this->assertNull($user?->profile);
        $this->assertSame('Acme SAS', $user?->companyProfile?->company_name);
    }

    public function test_company_registration_requires_company_fields(): void
    {
        $response = $this->from('/register')->post('/register', $this->validPayload([
            'role' => 'company',
            'email' => 'company2@example.com',
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['representative_name', 'representative_email', 'company_need']);
    }

    public function test_talent_registration_requires_sector_and_documents(): void
    {
        $response = $this->from('/register')->post('/register', $this->validPayload());

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['sector', 'description', 'documents']);
    }

    public function test_verified_pending_talent_cannot_access_dashboard(): void
    {
        $user = User::factory()->unverified()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
        ]);
        $user->markEmailAsVerified();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('account.pending'));
    }

    public function test_admin_can_approve_pending_talent(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);
        $talent = User::factory()->create([
            'role' => 'dev',
            'approval_status' => User::APPROVAL_PENDING,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.approve', $talent));

        $response->assertRedirect();
        $talent->refresh();
        $this->assertTrue($talent->isApproved());
        $this->assertNotNull($talent->profile);
    }

    public function test_registration_requires_a_valid_role(): void
    {
        $response = $this->from('/register')->post('/register', $this->validTalentPayload([
            'role' => 'admin',
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }

    public function test_registration_rejects_honeypot_field(): void
    {
        $response = $this->from('/register')->post('/register', $this->validTalentPayload([
            'website' => 'https://spam.example',
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('website');
        $this->assertGuest();
    }

    public function test_registration_rejects_invalid_name_characters(): void
    {
        $response = $this->from('/register')->post('/register', $this->validTalentPayload([
            'name' => '<script>alert(1)</script>',
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('name');
        $this->assertGuest();
    }

    public function test_registration_rejects_weak_password(): void
    {
        $response = $this->from('/register')->post('/register', $this->validTalentPayload([
            'password' => 'password',
            'password_confirmation' => 'password',
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registration_is_rate_limited_after_repeated_failures(): void
    {
        RateLimiter::clear('register|test@example.com|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->from('/register')->post('/register', $this->validTalentPayload([
                'email' => 'test@example.com',
                'password' => 'short',
                'password_confirmation' => 'short',
            ]));
        }

        $response = $this->from('/register')->post('/register', $this->validTalentPayload([
            'email' => 'test@example.com',
        ]));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_does_not_allow_privilege_escalation_fields(): void
    {
        Mail::fake();

        $this->post('/register', $this->validTalentPayload([
            'is_subscribed' => true,
            'subscription_expires_at' => now()->addYear()->toDateTimeString(),
        ]));

        $pending = PendingRegistration::query()->where('email', 'test@example.com')->firstOrFail();
        $this->get(route('register.verify', ['token' => $pending->token]));

        $user = User::query()->where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertFalse($user->is_subscribed);
        $this->assertNull($user->subscription_expires_at);
    }
}
