<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\ProfessionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', $this->validTalentPayload());

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('toast_success');
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'dev',
        ]);
        $this->assertNull(User::query()->where('email', 'test@example.com')->value('email_verified_at'));
        $user = User::query()->where('email', 'test@example.com')->first();
        $this->assertNotNull($user->profile);
        $this->assertSame('Développeur passionné avec plus de cinq ans d\'expérience en Laravel et React.', $user->profile->registration_description);
        $this->assertCount(1, $user->profile->documents);
        $this->assertSame(User::APPROVAL_PENDING, $user->approval_status);
    }

    public function test_company_registration_does_not_require_talent_fields(): void
    {
        $response = $this->post('/register', $this->validPayload([
            'role' => 'company',
            'email' => 'company@example.com',
        ]));

        $response->assertRedirect(route('login'));
        $this->assertNull(User::query()->where('email', 'company@example.com')->first()?->profile);
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
        $this->post('/register', $this->validTalentPayload([
            'is_subscribed' => true,
            'subscription_expires_at' => now()->addYear()->toDateTimeString(),
        ]));

        $user = User::query()->where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertFalse($user->is_subscribed);
        $this->assertNull($user->subscription_expires_at);
    }
}
