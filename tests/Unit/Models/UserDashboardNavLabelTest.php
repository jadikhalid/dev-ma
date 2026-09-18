<?php

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserDashboardNavLabelTest extends TestCase
{
    #[Test]
    public function dashboard_nav_label_keeps_tdb_only_for_site_admin(): void
    {
        app()->setLocale('fr');

        $talent = User::factory()->talent()->make();
        $company = User::factory()->companyOwner()->make();
        $admin = User::factory()->make(['role' => 'admin']);

        $this->assertSame('Tableau de bord', $talent->dashboardNavLabel());
        $this->assertSame('Tableau de bord', $company->dashboardNavLabel());
        $this->assertSame('TDB', $admin->dashboardNavLabel());
    }
}
