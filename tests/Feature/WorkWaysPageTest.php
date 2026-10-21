<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkWaysPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_cards_open_a_page_for_each_way_of_working(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('career.ways.show', 'full-remote'), false)
            ->assertSee(route('career.ways.show', 'sponsorship-visa'), false)
            ->assertSee(route('career.ways.show', 'placement-local'), false);

        foreach ([
            'full-remote' => 'remote',
            'sponsorship-visa' => 'visa',
            'placement-local' => 'local',
        ] as $slug => $key) {
            $this->get(route('career.ways.show', $slug))
                ->assertOk()
                ->assertSee(__('talenma.work_ways.modes.'.$key.'.title'), false)
                ->assertSee(__('talenma.work_ways.role_title'), false)
                ->assertSee(__('talenma.work_ways.modes.'.$key.'.boundary'), false);
        }

        $this->get('/travailler/inconnu')->assertNotFound();
    }
}
