<?php

namespace Tests\Feature;

use App\Http\Controllers\JadiDigitalContactController;
use App\Mail\JadiDigitalContactMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class JadiDigitalPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_lists_offers_and_contact(): void
    {
        $response = $this->get(route('jadi-digital'))
            ->assertOk()
            ->assertSee('data-jadi-page', false)
            ->assertSee(__('talenma.jadi_page.title'))
            ->assertSee(__('talenma.jadi_page.offers_title'))
            ->assertSee(__('talenma.jadi_page.offer_services_label'))
            ->assertSee('data-jadi-founder', false)
            ->assertSee('images/brand/jadi-founder.jpg', false)
            ->assertSee(__('talenma.jadi_page.founder_skills_title'))
            ->assertSee('id="jadi-contact-form"', false)
            ->assertSee('data-ajax', false)
            ->assertSee(route('jadi-digital.contact'), false)
            ->assertDontSee('Mobhookah', false)
            ->assertDontSee('+212', false)
            ->assertDontSee('mailto:khalid@jadi-digital.com', false);

        $this->assertSame(
            count(__('talenma.jadi_page.offers')),
            substr_count($response->getContent(), 'data-jadi-offer')
        );
    }

    public function test_contact_form_sends_mail_via_ajax(): void
    {
        Mail::fake();

        $this->postJson(route('jadi-digital.contact'), [
            'name' => 'Amina Benali',
            'email' => 'amina@example.com',
            'company' => 'Acme SAS',
            'message' => 'Bonjour, nous souhaitons discuter d\'un projet digital.',
        ])
            ->assertOk()
            ->assertJson([
                'message' => __('talenma.jadi_page.contact_form.sent'),
            ]);

        Mail::assertSent(JadiDigitalContactMail::class, function (JadiDigitalContactMail $mail) {
            return $mail->hasTo(JadiDigitalContactController::RECIPIENT)
                && $mail->name === 'Amina Benali'
                && $mail->email === 'amina@example.com'
                && $mail->company === 'Acme SAS';
        });
    }

    public function test_contact_form_validates_fields(): void
    {
        Mail::fake();

        $this->postJson(route('jadi-digital.contact'), [
            'name' => 'A',
            'email' => 'not-an-email',
            'message' => 'Trop court',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        Mail::assertNothingSent();
    }

    public function test_contact_honeypot_skips_mail(): void
    {
        Mail::fake();

        $this->postJson(route('jadi-digital.contact'), [
            'name' => 'Bot User',
            'email' => 'bot@example.com',
            'message' => 'Message suffisamment long pour passer la validation.',
            'website' => 'https://spam.example',
        ])
            ->assertOk()
            ->assertJson([
                'message' => __('talenma.jadi_page.contact_form.sent'),
            ]);

        Mail::assertNothingSent();
    }

    public function test_footer_credit_links_to_the_page(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('jadi-digital').'" class="font-semibold text-indigo-300', false);
    }
}
