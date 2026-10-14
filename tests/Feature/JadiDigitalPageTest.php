<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadiDigitalPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_lists_offers_case_study_and_contact(): void
    {
        $response = $this->get(route('jadi-digital'))
            ->assertOk()
            ->assertSee('data-jadi-page', false)
            ->assertSee(__('talenma.jadi_page.title'))
            ->assertSee(__('talenma.jadi_page.case_title'))
            ->assertSee('mailto:khalid@jadi-digital.com', false);

        $this->assertSame(
            count(__('talenma.jadi_page.offers')),
            substr_count($response->getContent(), 'data-jadi-offer')
        );
    }

    public function test_footer_credit_links_to_the_page(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('jadi-digital').'" class="font-semibold text-indigo-300', false);
    }
}
