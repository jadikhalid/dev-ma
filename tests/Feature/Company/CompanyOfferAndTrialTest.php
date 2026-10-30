<?php

namespace Tests\Feature\Company;

use App\Mail\CompanyApprovedMail;
use App\Mail\CompanyDemoConfirmationMail;
use App\Mail\CompanyDemoRequestMail;
use App\Mail\CompanyTrialRequestMail;
use App\Mail\VerifyRegistrationMail;
use App\Models\PendingRegistration;
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

    public function test_demo_form_shows_booking_days_as_weekday_calendar(): void
    {
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-10-28 10:00:00'));

        $response = $this->get(route('company.demo'))->assertOk();
        $html = $response->getContent();

        $response
            ->assertSee('data-demo-preferred-date', false)
            ->assertSee('Octobre – novembre 2026')
            ->assertSee('value="2026-10-29"', false)
            ->assertSee('value="2026-11-18"', false)
            ->assertDontSee('value="2026-10-31"', false)
            ->assertDontSee('value="2026-11-19"', false)
            ->assertDontSee('<select id="demo_preferred_date"', false);

        $this->assertSame(CompanyDemoRequest::BOOKING_WEEKDAYS_AHEAD, substr_count($html, 'type="radio" name="preferred_date"'));
    }

    public function test_company_offer_page_is_public(): void
    {
        $response = $this->get(route('company.offer'));

        $response
            ->assertOk()
            ->assertSee(__('talenma.company_offer.hero_cta_demo'), false)
            ->assertSee(__('talenma.company_offer.hero_cta_trial'), false)
            ->assertSee('href="'.route('company.trial').'"', false)
            ->assertDontSee(__('talenma.company_offer.trial_submit'), false)
            ->assertDontSee(__('talenma.nav.jobs'), false)
            ->assertDontSee(__('talenma.nav.blog'), false)
            ->assertDontSee(__('talenma.nav.apps_launcher_title'), false)
            ->assertDontSee('data-trial-wizard', false)
            ->assertDontSee('name="role"', false);

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
            ->assertDontSee('data-company-offer-drawer', false)
            ->assertSee('href="'.route('company.demo').'"', false)
            ->assertSee('href="'.route('company.trial').'"', false)
            ->assertSee('<p class="text-xl font-extrabold text-gray-950 sm:text-3xl" data-company-offer-talent-count>2+</p>', false);
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
            ->assertSee('data-footer-jadi-link', false)
            ->assertDontSee(route('newsletter.subscribe'), false)
            ->assertDontSee(__('talenma.footer.developer_name'), false);
    }

    public function test_company_header_drops_case_studies_and_about_and_offers_a_trial(): void
    {
        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('data-company-header-demo', false)
            ->assertSee('data-company-header-trial', false)
            ->assertSee('href="'.route('company.trial').'"', false)
            ->assertSee(__('talenma.company_offer.hero_cta_trial'), false)
            ->assertSee(__('talenma.nav.companies_badge'), false)
            ->assertSee('data-company-header-talent-space', false)
            ->assertSee(__('talenma.nav.talent_space'), false)
            ->assertDontSee('data-company-platform-trigger', false)
            ->assertDontSee('data-company-platform-menu', false)
            ->assertSee(route('company.offers'), false)
            ->assertDontSee(__('talenma.nav.case_studies'), false)
            ->assertDontSee(__('talenma.nav.about'), false);

        $this->get(route('company.offers'))
            ->assertOk()
            ->assertSee('data-company-header-band', false)
            ->assertSee('data-company-offers', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_offers_page_shows_trial_and_paid_plans_on_quote(): void
    {
        $this->get(route('company.offers'))
            ->assertOk()
            ->assertSee('data-company-offers', false)
            ->assertSee('data-company-offers-hero', false)
            ->assertSee(__('talenma.company_plans.plans.trial.name'), false)
            ->assertSee(__('talenma.company_plans.plans.essential.name'), false)
            ->assertSee(__('talenma.company_plans.plans.pro.name'), false)
            ->assertSee(__('talenma.company_plans.plans.custom.name'), false)
            ->assertSee(__('talenma.company_plans.price_trial_label'), false)
            ->assertSee(__('talenma.company_plans.price_essential_label'), false)
            ->assertSee(__('talenma.company_plans.price_pro_label'), false)
            ->assertDontSee('1 490', false)
            ->assertDontSee('2 990', false)
            ->assertDontSee('€1,490', false)
            ->assertDontSee('€2,990', false)
            ->assertDontSee('Produit d’appel', false)
            ->assertDontSee('pour toujours', false)
            ->assertSee(__('talenma.company_plans.custom.title'), false)
            ->assertSee(__('talenma.company_plans.ats.badge'), false)
            ->assertSee(__('talenma.company_plans.formulas_title'), false)
            ->assertSee(route('company.demo'), false)
            ->assertSee(route('company.trial'), false)
            ->assertDontSee(route('company.offer').'#trial', false);
    }

    public function test_mobile_nav_drawer_lists_band_links_and_offer_ctas(): void
    {
        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('data-company-mobile-nav-toggle', false)
            ->assertSee('data-company-mobile-home', false)
            ->assertSee('data-company-mobile-nav', false)
            ->assertDontSee('data-company-mobile-platform-toggle', false)
            ->assertSee('data-company-mobile-talent-space', false)
            ->assertSee('data-company-mobile-demo', false)
            ->assertSee('data-company-mobile-trial', false);
    }

    public function test_company_header_links_talent_space_to_www_portal(): void
    {
        $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('data-company-header-talent-space', false)
            ->assertSee('href="'.e(\App\Support\PortalHost::wwwRootUrl()).'"', false)
            ->assertSee('target="_blank"', false)
            ->assertSee(__('talenma.nav.talent_space'), false)
            ->assertDontSee('data-company-platform-trigger', false)
            ->assertDontSee('data-company-platform-menu', false)
            ->assertDontSee('data-company-mobile-platform-toggle', false);
    }

    public function test_company_offer_shows_a_showcase_section_for_each_platform_service(): void
    {
        $response = $this->get(route('company.offer'))
            ->assertOk()
            ->assertSee('data-company-platform-intro', false)
            ->assertSee(__('talenma.company_offer.platform_title'))
            ->assertSee('data-company-platform-final', false);

        foreach (['catalogue', 'applications', 'sourcing', 'jobs'] as $key) {
            $response
                ->assertSee('data-company-platform-service="'.$key.'"', false)
                ->assertSee('id="platform-'.$key.'"', false)
                ->assertSee(__('talenma.company_offer.services.'.$key.'.title'));
        }
    }

    public function test_logged_in_company_home_redirects_to_dashboard(): void
    {
        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        $this->actingAs($company)
            ->get(route('company.offer', ['tab' => 'trial']))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($company)
            ->get(route('company.trial'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_logged_in_company_header_has_no_home_link_and_logo_targets_dashboard(): void
    {
        $company = User::factory()->companyOwner()->create();
        CompanyProfile::factory()->onTrial()->create(['user_id' => $company->id]);

        $this->actingAs($company)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('data-nav-home-link', false)
            ->assertDontSee('href="'.route('company.offer').'"', false);
    }

    public function test_company_offer_trial_tab_opens_trial_page(): void
    {
        $this->get(route('company.offer', ['tab' => 'trial']))
            ->assertRedirect(route('company.trial'));
    }

    public function test_trial_request_page_shows_the_form(): void
    {
        $this->get(route('company.trial'))
            ->assertOk()
            ->assertSee('data-company-trial', false)
            ->assertSee(__('talenma.company_offer.trial_title'), false)
            ->assertSee(__('talenma.company_offer.trial_submit'), false)
            ->assertSee('name="first_name"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('data-trial-wizard', false)
            ->assertSee(route('company.trial.store'), false)
            ->assertSee('name="trial_terms"', false)
            ->assertSee(route('company.trial.terms'), false)
            ->assertSee('data-company-header-trial', false)
            ->assertSee('bg-gray-200', false)
            ->assertSee('data-company-mobile-trial', false)
            ->assertDontSee('data-company-offer-drawer="trial"', false);
    }

    public function test_register_company_query_redirects_to_trial_page(): void
    {
        $this->get(route('register', ['role' => 'company']))
            ->assertRedirect(route('company.trial'));
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

        $this->from(route('company.trial'))
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
            ->assertRedirect(route('company.trial'))
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

        $response = $this->post(route('company.demo.store'), $this->demoPayload());

        $response->assertRedirect(route('company.demo'));
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

        Mail::assertSent(CompanyDemoConfirmationMail::class, function (CompanyDemoConfirmationMail $mail) {
            return $mail->hasTo('jean@acme.test')
                && $mail->demoRequest->company_name === 'Acme SAS';
        });
        $this->assertSame(CompanyDemoRequest::STATUS_NEW, CompanyDemoRequest::query()->first()->status);
    }

    public function test_demo_confirmation_mail_mentions_company_and_message(): void
    {
        $demo = $this->makeDemoRequest();

        $html = (new CompanyDemoConfirmationMail($demo))->render();

        $this->assertStringContainsString('Acme SAS', $html);
        $this->assertStringContainsString('Jean Dupont', $html);
        $this->assertStringContainsString('Nous voulons voir la plateforme.', $html);
    }

    public function test_admin_can_list_demo_requests_by_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->makeDemoRequest(['company_name' => 'Nouvelle SARL']);
        $this->makeDemoRequest(['company_name' => 'Planifiee SA', 'status' => CompanyDemoRequest::STATUS_SCHEDULED]);

        $this->actingAs($admin)
            ->get(route('admin.company-demo-requests.index'))
            ->assertOk()
            ->assertSee('Nouvelle SARL')
            ->assertDontSee('Planifiee SA')
            ->assertSee('data-demo-new-count', false);

        $this->actingAs($admin)
            ->get(route('admin.company-demo-requests.index', ['status' => CompanyDemoRequest::STATUS_SCHEDULED]))
            ->assertOk()
            ->assertSee('Planifiee SA')
            ->assertDontSee('Nouvelle SARL');
    }

    public function test_admin_can_update_demo_request_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $demo = $this->makeDemoRequest();

        $this->actingAs($admin)
            ->patch(route('admin.company-demo-requests.status', $demo), ['status' => CompanyDemoRequest::STATUS_SCHEDULED])
            ->assertRedirect()
            ->assertSessionHas('toast_success');

        $demo->refresh();
        $this->assertSame(CompanyDemoRequest::STATUS_SCHEDULED, $demo->status);
        $this->assertSame($admin->id, $demo->handled_by);
        $this->assertNotNull($demo->handled_at);

        $this->actingAs($admin)
            ->patch(route('admin.company-demo-requests.status', $demo), ['status' => CompanyDemoRequest::STATUS_NEW]);

        $demo->refresh();
        $this->assertSame(CompanyDemoRequest::STATUS_NEW, $demo->status);
        $this->assertNull($demo->handled_by);
        $this->assertNull($demo->handled_at);

        $this->actingAs($admin)
            ->patch(route('admin.company-demo-requests.status', $demo), ['status' => 'bogus'])
            ->assertSessionHasErrors('status');
    }

    public function test_non_staff_cannot_access_demo_requests(): void
    {
        $company = User::factory()->create(['role' => 'company']);
        $demo = $this->makeDemoRequest();

        $this->actingAs($company)
            ->get(route('admin.company-demo-requests.index'))
            ->assertForbidden();

        $this->actingAs($company)
            ->patch(route('admin.company-demo-requests.status', $demo), ['status' => CompanyDemoRequest::STATUS_DONE])
            ->assertForbidden();

        $this->assertSame(CompanyDemoRequest::STATUS_NEW, $demo->fresh()->status);
    }

    public function test_admin_header_links_sit_in_an_overflow_row_with_more_menu(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.company-demo-requests.index'))
            ->assertOk()
            ->assertSee('x-data="navOverflow"', false)
            ->assertSee('data-nav-overflow-row', false)
            ->assertSee('data-nav-more-button', false)
            ->assertSee('aria-label="'.__('talenma.nav.more').'"', false)
            ->assertSee(__('talenma.nav.admin_company_demos'));
    }

    public function test_demo_request_stores_qualification_answers_and_combines_phone(): void
    {
        Mail::fake();
        User::factory()->create(['role' => 'admin']);

        $this->postJson(route('company.demo.store'), $this->demoPayload([
            'phone_country' => 'ma',
            'phone' => '0622119177',
            'hiring_locations' => ['ma', 'fr', 'ma'],
            'message' => '',
        ]))->assertOk();

        $demo = CompanyDemoRequest::query()->firstOrFail();
        $this->assertSame('Jean', $demo->first_name);
        $this->assertSame('Dupont', $demo->last_name);
        $this->assertSame('Jean Dupont', $demo->contact_name);
        $this->assertSame('+212 622119177', $demo->phone);
        $this->assertSame('11-50', $demo->company_size);
        $this->assertSame('1-4', $demo->hires_planned);
        $this->assertSame(['ma', 'fr'], $demo->hiring_locations);
        $this->assertSame('Casablanca', $demo->hiring_city);
        $this->assertSame('no', $demo->uses_ats);
        $this->assertNull($demo->message);
        $this->assertSame($this->demoPreferredDate(), $demo->preferred_date?->toDateString());
        $this->assertSame(['10:00', '14:00'], $demo->preferred_slots);
        $this->assertSame('teams', $demo->meeting_platform);
    }

    public function test_demo_request_requires_qualification_fields(): void
    {
        $this->postJson(route('company.demo.store'), $this->demoPayload([
            'phone' => '',
            'company_size' => '',
            'hires_planned' => '999',
            'hiring_locations' => [],
            'uses_ats' => '',
            'preferred_date' => '',
            'preferred_slots' => [],
            'meeting_platform' => '',
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'phone',
                'company_size',
                'hires_planned',
                'hiring_locations',
                'uses_ats',
                'preferred_date',
                'preferred_slots',
                'meeting_platform',
            ]);

        $this->assertSame(0, CompanyDemoRequest::query()->count());
    }

    public function test_demo_request_rejects_weekend_and_invalid_slot(): void
    {
        Mail::fake();
        User::factory()->create(['role' => 'admin']);

        $this->postJson(route('company.demo.store'), $this->demoPayload([
            'preferred_date' => now()->next('Saturday')->toDateString(),
            'preferred_slots' => ['08:00'],
            'meeting_platform' => 'skype',
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['preferred_date', 'preferred_slots.0', 'meeting_platform']);
    }

    public function test_demo_page_renders_the_wizard_and_disables_the_header_button(): void
    {
        $this->get(route('company.demo'))
            ->assertOk()
            ->assertSee('data-company-demo', false)
            ->assertSee('data-company-header-demo', false)
            ->assertSee('border-gray-300 bg-gray-100', false)
            ->assertSee('data-company-mobile-demo', false)
            ->assertSee('data-demo-wizard', false)
            ->assertSee('data-demo-step="1"', false)
            ->assertSee('data-demo-step="2"', false)
            ->assertSee('data-demo-step="3"', false)
            ->assertSee('data-demo-step="4"', false)
            ->assertSee('name="first_name"', false)
            ->assertSee('name="hiring_locations[]"', false)
            ->assertSee('name="preferred_slots[]"', false)
            ->assertSee('name="meeting_platform"', false)
            ->assertSee('data-demo-cancel', false)
            ->assertSee(__('talenma.company_offer.demo_form.cancel'))
            ->assertDontSee('calendly.com', false)
            ->assertSee(__('talenma.company_offer.demo_form.thanks_title'));
    }

    public function test_company_offer_renders_trial_wizard_steps(): void
    {
        $this->get(route('company.trial'))
            ->assertOk()
            ->assertSee('data-trial-wizard', false)
            ->assertSee('data-trial-step="1"', false)
            ->assertSee('data-trial-step="2"', false)
            ->assertSee('data-trial-step="3"', false)
            ->assertSee('data-trial-cancel', false)
            ->assertSee(__('talenma.company_offer.trial_form.thanks_title'));
    }

    public function test_admin_demo_page_shows_qualification_answers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->makeDemoRequest([
            'company_size' => '51-200',
            'hires_planned' => '5-10',
            'hiring_locations' => ['ma'],
            'uses_ats' => 'yes',
            'preferred_date' => $this->demoPreferredDate(),
            'preferred_slots' => ['09:00', '11:00'],
            'meeting_platform' => 'zoom',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.company-demo-requests.index'))
            ->assertOk()
            ->assertSee('data-demo-needs', false)
            ->assertSee('51-200')
            ->assertSee(__('talenma.company_offer.demo_form.locations.ma'))
            ->assertSee('09:00 – 10:00')
            ->assertSee(__('talenma.company_offer.demo_form.platforms.zoom'));
    }

    private function demoPreferredDate(): string
    {
        return CompanyDemoRequest::availableBookingDates()->first()->toDateString();
    }

    private function demoPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@acme.test',
            'phone_country' => 'fr',
            'phone' => '+33123456789',
            'company_name' => 'Acme SAS',
            'company_size' => '11-50',
            'hires_planned' => '1-4',
            'hiring_locations' => ['ma'],
            'hiring_city' => 'Casablanca',
            'uses_ats' => 'no',
            'preferred_date' => $this->demoPreferredDate(),
            'preferred_slots' => ['10:00', '14:00'],
            'meeting_platform' => 'teams',
            'message' => 'Bonjour, je souhaite une démonstration de la plateforme pour notre équipe RH.',
        ], $overrides);
    }

    private function makeDemoRequest(array $overrides = []): CompanyDemoRequest
    {
        return CompanyDemoRequest::query()->create(array_merge([
            'company_name' => 'Acme SAS',
            'contact_name' => 'Jean Dupont',
            'email' => 'jean@acme.test',
            'phone' => '+33123456789',
            'message' => 'Nous voulons voir la plateforme.',
            'locale' => 'fr',
        ], $overrides));
    }

    public function test_demo_request_can_be_submitted_via_ajax(): void
    {
        Mail::fake();

        User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com',
        ]);

        $response = $this->postJson(route('company.demo.store'), $this->demoPayload([
            'email' => 'ajax-demo@acme.test',
            'message' => '',
        ]));

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

    public function test_trial_request_sends_a_confirmation_link_before_opening_the_account(): void
    {
        Mail::fake();

        $response = $this->post(route('company.trial.store'), $this->trialPayload());

        $response->assertRedirect(route('company.offer'));
        $response->assertSessionHas('toast_sticky_success', __('talenma.company_offer.trial_sent'));
        $response->assertSessionHas('pending_registration_email', 'trial@acme.test');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'trial@acme.test']);

        $pending = PendingRegistration::query()->where('email', 'trial@acme.test')->first();
        $this->assertNotNull($pending);
        $this->assertTrue($pending->payload['company_trial']);

        Mail::assertSent(VerifyRegistrationMail::class, function (VerifyRegistrationMail $mail) {
            return $mail->hasTo('trial@acme.test');
        });
        Mail::assertNotSent(CompanyApprovedMail::class);
        Mail::assertNotSent(CompanyTrialRequestMail::class);

        $this->get(route('register.verify', ['token' => $pending->token]))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $user = User::query()->where('email', 'trial@acme.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->isApproved());
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('Secret123', $user->password));
        $this->assertNull($user->companyProfile?->description);
        $this->assertTrue($user->companyProfile?->isOnTrial());

        $this->assertDatabaseHas('company_trial_requests', [
            'company_name' => 'Acme SAS',
            'email' => 'trial@acme.test',
            'contact_name' => 'Jean Dupont',
            'status' => CompanyTrialRequest::STATUS_PROVISIONED,
            'user_id' => $user->id,
            'trial_terms_version' => config('talenma.trial_terms_version'),
        ]);

        $pending->refresh();
        $this->assertNotNull($pending->completed_at);
        $this->assertSame($user->id, $pending->user_id);

        // Second click (email prefetch / double open) must reopen the account, not the trial form.
        $this->get(route('register.verify', ['token' => $pending->token]))
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::query()->where('email', 'trial@acme.test')->count());
    }

    public function test_company_verify_on_talent_host_finishes_on_company_portal(): void
    {
        Mail::fake();

        $this->post(route('company.trial.store'), $this->trialPayload([
            'email' => 'cross-host@acme.test',
        ]));

        $pending = PendingRegistration::query()->where('email', 'cross-host@acme.test')->firstOrFail();
        $www = config('talenma.hosts.www');

        $response = $this->get('http://'.$www.'/register/verify/'.$pending->token);

        $user = User::query()->where('email', 'cross-host@acme.test')->firstOrFail();
        $this->assertGuest();

        $response->assertRedirect();
        $location = $response->headers->get('Location');
        $this->assertNotNull($location);
        $this->assertStringContainsString('entreprises.', parse_url($location, PHP_URL_HOST) ?? '');
        $this->assertStringContainsString('/register/company-finish/'.$user->id, $location);

        $this->get($location)
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_trial_request_can_be_submitted_via_ajax(): void
    {
        Mail::fake();

        $response = $this->postJson(route('company.trial.store'), $this->trialPayload([
            'email' => 'ajax-trial@acme.test',
            'phone_country' => 'ma',
            'phone' => '0612345678',
            'company_description' => 'Courte description.',
        ]));

        $response
            ->assertOk()
            ->assertJson([
                'message' => __('talenma.company_offer.trial_sent'),
                'redirect' => route('company.offer'),
            ])
            ->assertSessionHas('toast_sticky_success', __('talenma.company_offer.trial_sent'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'ajax-trial@acme.test']);
        $this->assertDatabaseHas('pending_registrations', ['email' => 'ajax-trial@acme.test']);
        Mail::assertSent(VerifyRegistrationMail::class);
    }

    public function test_trial_request_rejects_a_personal_email(): void
    {
        $this->post(route('company.trial.store'), $this->trialPayload([
            'email' => 'jean@gmail.com',
        ]))->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('pending_registrations', ['email' => 'jean@gmail.com']);
        $this->assertDatabaseMissing('users', ['email' => 'jean@gmail.com']);
    }

    public function test_trial_request_requires_accepted_terms(): void
    {
        $this->post(route('company.trial.store'), [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'company_name' => 'Acme SAS',
            'email' => 'no-terms@acme.test',
            'phone_country' => 'fr',
            'phone' => '+33123456789',
            'sector' => 'it-digital',
            'company_description' => 'Nous sommes une entreprise spécialisée dans le développement web et mobile.',
            'company_country' => 'fr',
            'data_processing_consent' => '1',
        ])->assertSessionHasErrors('trial_terms');

        $this->assertDatabaseMissing('company_trial_requests', ['email' => 'no-terms@acme.test']);
    }

    public function test_trial_terms_page_is_public(): void
    {
        $this->get(route('company.trial.terms'))
            ->assertOk()
            ->assertSee(__('talenma.company_offer.trial_terms.title'), false)
            ->assertSee(__('talenma.company_offer.trial_terms.sections.0.title'), false)
            ->assertSee('912 345 678', false)
            ->assertSee('Paris', false)
            ->assertSee('SASU', false);
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

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function trialPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'company_name' => 'Acme SAS',
            'email' => 'trial@acme.test',
            'password' => 'Secret123',
            'password_confirmation' => 'Secret123',
            'phone_country' => 'fr',
            'phone' => '+33123456789',
            'sector' => 'it-digital',
            'company_country' => 'fr',
            'data_processing_consent' => '1',
            'trial_terms' => '1',
        ], $overrides);
    }
}
