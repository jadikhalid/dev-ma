<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class PrivacyPolicyTest extends TestCase
{
    public function test_privacy_policy_page_can_be_rendered(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee(__('talenma.privacy.title'), false)
            ->assertSee(__('talenma.privacy.sections.cookies.title'), false)
            ->assertSee('talenma_mobile_locale_checked', false);
    }
}
