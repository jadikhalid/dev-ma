<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlatformSettingController extends Controller
{
    public function updateTalentValidation(Request $request): RedirectResponse
    {
        $request->validate([
            'require_talent_admin_validation' => ['required', 'boolean'],
        ]);

        $enabled = $request->boolean('require_talent_admin_validation');

        PlatformSetting::setRequiresTalentAdminValidation($enabled);

        return back()->with(
            'status',
            $enabled
                ? __('talenma.dashboard.admin.talent_validation_enabled')
                : __('talenma.dashboard.admin.talent_validation_disabled')
        );
    }

    public function updateTalentEmailVerification(Request $request): RedirectResponse
    {
        $request->validate([
            'require_talent_email_verification' => ['required', 'boolean'],
        ]);

        $enabled = $request->boolean('require_talent_email_verification');

        PlatformSetting::setRequiresTalentEmailVerification($enabled);

        return back()->with(
            'status',
            $enabled
                ? __('talenma.dashboard.admin.talent_email_verification_enabled')
                : __('talenma.dashboard.admin.talent_email_verification_disabled')
        );
    }
}
