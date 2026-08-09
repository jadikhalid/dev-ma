<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class PrivacyPolicyTest extends TestCase
{
    public function test_privacy_policy_page_can_be_rendered(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee(__('talenma.privacy.title'), false);
    }
}
