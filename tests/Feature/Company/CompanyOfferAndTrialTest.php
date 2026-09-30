<?php

namespace Tests\Feature\Company;

use App\Mail\CompanyApprovedMail;
use App\Mail\CompanyDemoRequestMail;
use App\Mail\CompanyTrialRequestMail;
use App\Models\CompanyDemoRequest;
use App\Models\CompanyProfile;
use App\Models\CompanyTrialRequest;
use App\Models\User;
use App\Services\UserModerationService;
use Database\Seeders\ProfessionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CompanyOfferAndTrialTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(ProfessionSeeder::class);
    }

    public function test_company_offer_page_is_public(): void
    {
        $response = $this->get(route('company.offer'));

        $response
            ->assertOk()
            ->assertSee(__('talenma.company_offer.cta_demo'), false)
            ->assertSee(__('talenma.company_offer.cta_trial'), false)
            ->assertSee(__('talenma.company_offer.trial_submit'), false)
            ->assertDontSee(__('talenma.nav.jobs'), false)
            ->assertDontSee(__('talenma.nav.blog'), false)
            ->assertDontSee(__('talenma.nav.apps_launcher_title'), false)
            ->assertSee('name="contact_name"', false)
            ->assertSee('name="phone"', false)
            ->assertDontSee('name="role"', false)
            ->assertSee(route('company.trial.store'), false);

        $this->assertSame(1, substr_count($response->getContent(), 'name="password"'));
        $response->assertSee('id="company-login-password"', false);

        $response->assertSeeText(__('talenma.company_offer.includes_1'));
        $response->assertSeeText(__('talenma.company_offer.includes_2'));
        $response->assertSeeText(__('talenma.company_offer.includes_3'));
        $response->assertSeeText(__('talenma.company_offer.includes_4'));
    }

    public function test_company_offer_hero_renders_closed_form_drawers(): void
    {
        User::factory()->count(2)->create(['role' => 'dev', 'approval_status' => User::APPROVAL_APPROVED]);
        User::factory()->create(['role' => 'dev', 'approval_status' => User::APPROVAL_PENDING]);

        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSeeText(__('talenma.company_offer.hero_title'))
            ->assertSee('drawer: null', false)
            ->assertSee('data-company-offer-drawer="demo"', false)
            ->assertSee('data-company-offer-drawer="trial"', false)
            ->assertSee('<p class="text-2xl font-extrabold text-gray-950 sm:text-3xl" data-company-offer-talent-count>2+</p>', false);
    }

    public function test_company_offer_hero_shows_approved_company_count(): void
    {
        foreach ([User::APPROVAL_APPROVED, User::APPROVAL_APPROVED, User::APPROVAL_APPROVED, User::APPROVAL_PENDING] as $status) {
            $company = User::factory()->companyOwner()->create(['approval_status' => $status]);
            CompanyProfile::factory()->create(['user_id' => $company->id]);
        }

        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('data-company-offer-company-count>3+</p>', false);
    }

    public function test_company_offer_footer_has_no_newsletter_and_agency_credit_only(): void
    {
        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('<footer', false)
            ->assertSee(route('privacy'), false)
            ->assertSee('https://www.jadi-digital.com/', false)
            ->assertDontSee(route('newsletter.subscribe'), false)
            ->assertDontSee(__('talenma.footer.developer_name'), false);
    }

    public function test_logged_in_company_does_not_see_demo_and_trial_ctas_or_drawers(): void
    {
        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        $this->actingAs($company)
            ->get(route('company.offer', ['tab' => 'trial']))
            ->assertOk()
            ->assertSeeText(__('talenma.company_offer.hero_title'))
            ->assertDontSee(__('talenma.company_offer.hero_cta_demo'), false)
            ->assertDontSee(__('talenma.company_offer.hero_cta_trial'), false)
            ->assertDontSee('data-company-offer-drawer', false)
            ->assertDontSee(route('company.demo.store'), false)
            ->assertDontSee(route('company.trial.store'), false)
            ->assertSee('data-company-offer-dashboard-link', false);
    }

    public function test_company_offer_trial_tab_opens_trial_drawer(): void
    {
        $this->get(route('company.offer', ['tab' => 'trial']))
            ->assertOk()
            ->assertSee('drawer: \'trial\'', false);
    }

    public function test_register_company_query_redirects_to_offer(): void
    {
        $this->get(route('register', ['role' => 'company']))
            ->assertRedirect(route('company.offer', ['tab' => 'trial']));
    }

    public function test_register_page_is_talent_only(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee(__('talenma.auth.register_title_talent'), false)
            ->assertDontSee(__('talenma.auth.register_as'), false)
            ->assertSee('value="dev"', false);
    }

    public function test_company_cannot_self_register_via_register_endpoint(): void
    {
        Mail::fake();

        $this->from(route('company.offer', ['tab' => 'trial']))
            ->post('/register', [
                'name' => 'Acme SAS',
                'email' => 'company@example.com',
                'role' => 'company',
                'contact_name' => 'Jean Dupont',
                'phone' => '+33123456789',
                'sector' => 'it-digital',
                'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile.',
                'company_country' => 'fr',
                'data_processing_consent' => '1',
            ])
            ->assertRedirect(route('company.offer', ['tab' => 'trial']))
            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseMissing('users', ['email' => 'company@example.com']);
        $this->assertDatabaseMissing('company_trial_requests', ['email' => 'company@example.com']);
        Mail::assertNothingSent();
    }

    public function test_demo_request_is_stored_and_emailed(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $response = $this->post(route('company.demo.store'), [
            'company_name' => 'Acme SAS',
            'contact_name' => 'Jean Dupont',
            'email' => 'jean@acme.test',
            'phone' => '+33123456789',
            'message' => 'Bonjour, je souhaite une démonstration de la plateforme pour notre équipe RH.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast_success');

        $this->assertDatabaseHas('company_demo_requests', [
            'company_name' => 'Acme SAS',
            'email' => 'jean@acme.test',
        ]);

        Mail::assertSent(CompanyDemoRequestMail::class, function (CompanyDemoRequestMail $mail) use ($admin) {
            return $mail->hasTo($admin->email)
                && $mail->demoRequest->company_name === 'Acme SAS';
        });

        $this->assertSame(1, CompanyDemoRequest::query()->count());
    }

    public function test_demo_request_can_be_submitted_via_ajax(): void
    {
        Mail::fake();

        User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $response = $this->postJson(route('company.demo.store'), [
            'company_name' => 'Acme SAS',
            'contact_name' => 'Jean Dupont',
            'email' => 'ajax-demo@acme.test',
            'phone' => '+33123456789',
            'message' => 'Bonjour, je souhaite une démonstration de la plateforme pour notre équipe RH.',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => __('talenma.company_offer.demo_sent'),
            ]);

        $this->assertDatabaseHas('company_demo_requests', [
            'email' => 'ajax-demo@acme.test',
        ]);
        Mail::assertSent(CompanyDemoRequestMail::class);
    }

    public function test_trial_request_is_stored_and_emailed_without_creating_user(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $response = $this->post(route('company.trial.store'), [
            'company_name' => 'Acme SAS',
            'contact_name' => 'Jean Dupont',
            'email' => 'trial@acme.test',
            'phone' => '+33123456789',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile.',
            'company_country' => 'fr',
            'data_processing_consent' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast_success', __('talenma.company_offer.trial_sent'));

        $this->assertDatabaseHas('company_trial_requests', [
            'company_name' => 'Acme SAS',
            'email' => 'trial@acme.test',
            'contact_name' => 'Jean Dupont',
            'status' => CompanyTrialRequest::STATUS_PENDING,
        ]);
        $this->assertDatabaseMissing('users', ['email' => 'trial@acme.test']);

        Mail::assertSent(CompanyTrialRequestMail::class, function (CompanyTrialRequestMail $mail) use ($admin) {
            return $mail->hasTo($admin->email)
                && $mail->trialRequest->company_name === 'Acme SAS';
        });
    }

    public function test_trial_request_can_be_submitted_via_ajax(): void
    {
        Mail::fake();

        User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $response = $this->postJson(route('company.trial.store'), [
            'company_name' => 'Acme SAS',
            'contact_name' => 'Jean Dupont',
            'email' => 'ajax-trial@acme.test',
            'phone' => '+33123456789',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile.',
            'company_country' => 'fr',
            'data_processing_consent' => '1',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => __('talenma.company_offer.trial_sent'),
            ]);

        $this->assertDatabaseHas('company_trial_requests', [
            'email' => 'ajax-trial@acme.test',
            'status' => CompanyTrialRequest::STATUS_PENDING,
        ]);
        $this->assertDatabaseMissing('users', ['email' => 'ajax-trial@acme.test']);
        Mail::assertSent(CompanyTrialRequestMail::class);
    }

    public function test_admin_can_list_trial_requests(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        CompanyTrialRequest::query()->create([
            'company_name' => 'Acme Listing',
            'contact_name' => 'Jean Dupont',
            'email' => 'listing@acme.test',
            'phone' => '+33123456789',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile.',
            'company_country' => 'fr',
            'status' => CompanyTrialRequest::STATUS_PENDING,
            'locale' => 'fr',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.company-trial-requests.index'))
            ->assertOk()
            ->assertSee('Acme Listing');
    }

    public function test_admin_can_provision_trial_request_and_email_credentials(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $trial = CompanyTrialRequest::query()->create([
            'company_name' => 'Acme SAS',
            'contact_name' => 'Jean Dupont',
            'email' => 'provision@acme.test',
            'phone' => '+33123456789',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile.',
            'company_country' => 'fr',
            'status' => CompanyTrialRequest::STATUS_PENDING,
            'locale' => 'fr',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.company-trial-requests.provision', $trial))
            ->assertRedirect();

        $user = User::query()->where('email', 'provision@acme.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isApproved());
        $this->assertSame('Jean Dupont', $user->companyProfile?->representative_name);
        $this->assertTrue($user->companyProfile?->isOnTrial());

        $trial->refresh();
        $this->assertSame(CompanyTrialRequest::STATUS_PROVISIONED, $trial->status);
        $this->assertSame($user->id, $trial->user_id);

        Mail::assertSent(CompanyApprovedMail::class, function (CompanyApprovedMail $mail) use ($user) {
            return $mail->hasTo($user->email)
                && is_string($mail->plainPassword)
                && strlen($mail->plainPassword) >= 8
                && Hash::check($mail->plainPassword, $user->fresh()->password);
        });
    }

    public function test_approving_legacy_pending_company_starts_three_month_trial(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $company = User::factory()->companyOwner()->create([
            'approval_status' => User::APPROVAL_PENDING,
            'approved_at' => null,
            'password' => Hash::make('old-password'),
        ]);
        $profile = CompanyProfile::factory()->create([
            'user_id' => $company->id,
            'is_subscribed' => false,
            'trial_ends_at' => null,
            'subscription_expires_at' => null,
        ]);

        app(UserModerationService::class)->approveCompany($company, $admin);

        $profile->refresh();
        $company->refresh();

        $this->assertTrue($company->isApproved());
        $this->assertTrue($profile->isOnTrial());
        Mail::assertSent(CompanyApprovedMail::class);
    }

    public function test_expired_trial_blocks_talent_pool(): void
    {
        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->expired()->create([
            'user_id' => $company->id,
        ]);

        $this->assertFalse($company->fresh()->canAccessTalentPool());
        $this->assertTrue($company->fresh()->companyPlanExpired());

        $this->actingAs($company)
            ->get(route('company.search'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_active_trial_allows_talent_pool(): void
    {
        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create([
            'user_id' => $company->id,
        ]);

        $this->assertTrue($company->fresh()->canAccessTalentPool());

        $this->actingAs($company)
            ->get(route('company.search'))
            ->assertOk();
    }
}
