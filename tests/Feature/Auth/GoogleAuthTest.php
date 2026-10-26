<?php

namespace Tests\Feature\Auth;

use App\Mail\TalentWelcomeMail;
use App\Models\PendingRegistration;
use App\Models\PlatformSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_talent_register_and_login_offer_google(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee(route('auth.google.redirect', ['intent' => 'register']), false)
            ->assertSeeText(__('talenma.auth.google_register'));

        $this->get('/login')
            ->assertOk()
            ->assertSee(route('auth.google.redirect', ['intent' => 'login']), false)
            ->assertSeeText(__('talenma.auth.google_login'))
            ->assertDontSee(__('talenma.auth.google_login_unknown'), false);
    }

    public function test_company_host_does_not_start_google_auth(): void
    {
        $this->get('http://entreprises.localhost/auth/google')
            ->assertRedirect(route('company.offer'));
    }

    public function test_google_redirect_goes_to_google(): void
    {
        config([
            'services.google.client_id' => 'test-client',
            'services.google.client_secret' => 'test-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);

        $response = $this->get('/auth/google?intent=login');
        $response->assertRedirect();
        $location = (string) $response->headers->get('Location');
        $this->assertStringContainsString('https://accounts.google.com/o/oauth2/auth', $location);
        $this->assertStringContainsString('prompt=select_account', $location);
    }

    public function test_new_google_user_becomes_a_verified_talent(): void
    {
        Mail::fake();

        $this->fakeGoogleUser([
            'id' => 'google-new',
            'email' => 'New.Talent@gmail.com',
            'given_name' => 'Sara',
            'family_name' => 'El Amrani',
            'email_verified' => true,
        ]);

        $this->googleCallback('register')
            ->assertRedirect(route('account.pending', absolute: false));

        $user = User::query()->where('email', 'new.talent@gmail.com')->first();

        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $this->assertSame('dev', $user->role);
        $this->assertSame('google-new', $user->google_id);
        $this->assertSame('Sara', $user->first_name);
        $this->assertSame('El Amrani', $user->last_name);
        $this->assertNull($user->password);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotNull($user->data_processing_consent_at);
        $this->assertSame(config('talenma.data_processing_consent_version'), $user->data_processing_consent_version);
        $this->assertTrue($user->profile()->exists());
        $this->assertTrue($user->isPendingApproval());
        Mail::assertNothingSent();
    }

    public function test_approved_google_signup_sends_the_welcome_mail(): void
    {
        Mail::fake();
        PlatformSetting::setRequiresTalentAdminValidation(false);

        $this->fakeGoogleUser([
            'id' => 'google-approved',
            'email' => 'approved@gmail.com',
            'name' => 'Amina Benali',
        ]);

        $this->googleCallback('register')
            ->assertRedirect(route('dashboard', absolute: false));

        Mail::assertSent(TalentWelcomeMail::class);
    }

    public function test_known_google_account_signs_in(): void
    {
        $user = User::factory()->talent()->create([
            'google_id' => 'google-known',
            'email' => 'known@gmail.com',
        ]);

        $this->fakeGoogleUser([
            'id' => 'google-known',
            'email' => 'known@gmail.com',
        ]);

        $this->googleCallback('login')
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::query()->count());
    }

    public function test_login_does_not_create_a_google_account(): void
    {
        $this->fakeGoogleUser([
            'id' => 'google-stranger',
            'email' => 'stranger@gmail.com',
            'name' => 'Inconnu',
        ]);

        $this->googleCallback('login')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertSame(0, User::query()->count());
    }

    public function test_existing_email_is_not_linked_automatically(): void
    {
        User::factory()->talent()->create([
            'email' => 'taken@gmail.com',
        ]);

        $this->fakeGoogleUser([
            'id' => 'google-other',
            'email' => 'taken@gmail.com',
        ]);

        $this->googleCallback('register')
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertNull(User::query()->where('email', 'taken@gmail.com')->value('google_id'));
    }

    public function test_pending_registration_blocks_google_signup(): void
    {
        PendingRegistration::query()->create([
            'token' => PendingRegistration::generateToken(),
            'email' => 'pending@gmail.com',
            'locale' => 'fr',
            'payload' => ['role' => 'dev'],
            'expires_at' => now()->addHour(),
        ]);

        $this->fakeGoogleUser([
            'id' => 'google-pending',
            'email' => 'pending@gmail.com',
        ]);

        $this->googleCallback('register')
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertSame(0, User::query()->count());
    }

    public function test_disabled_google_account_cannot_sign_in(): void
    {
        User::factory()->talent()->create([
            'google_id' => 'google-disabled',
            'email' => 'disabled@gmail.com',
            'disabled_at' => now(),
        ]);

        $this->fakeGoogleUser([
            'id' => 'google-disabled',
            'email' => 'disabled@gmail.com',
        ]);

        $this->googleCallback('login')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    private function googleCallback(string $intent)
    {
        return $this->withSession(['google_auth_intent' => $intent])->get('/auth/google/callback');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function fakeGoogleUser(array $attributes): void
    {
        Socialite::fake('google', SocialiteUser::fake($attributes));
    }
}
