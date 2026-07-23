<?php

namespace App\Http\Controllers;

use App\Models\CompanyMembership;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CompanyUserController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $owner = $request->user();
        abort_unless($owner->canManageCompanyUsers(), 403);

        $profile = $owner->companyProfile;
        abort_unless($profile, 404);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:127'],
            'last_name' => ['required', 'string', 'min:2', 'max:127'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'job_title' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [], [
            'first_name' => __('talenma.company_users.first_name'),
            'last_name' => __('talenma.company_users.last_name'),
            'email' => __('talenma.company_users.email'),
            'job_title' => __('talenma.company_users.job_title'),
            'password' => __('talenma.company_users.password'),
        ]);

        $orgName = $profile->displayName() ?: $owner->name;
        $personName = trim($data['first_name'].' '.$data['last_name']);

        DB::transaction(function () use ($data, $owner, $profile, $orgName, $personName) {
            $member = User::create([
                'name' => $orgName.' / '.$personName,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'company',
                'company_seat' => User::SEAT_MEMBER,
                'email_verified_at' => now(),
                'approval_status' => User::APPROVAL_APPROVED,
                'approved_at' => now(),
                'approved_by' => $owner->id,
            ]);

            CompanyMembership::create([
                'company_profile_id' => $profile->id,
                'user_id' => $member->id,
                'job_title' => $data['job_title'] ?? null,
                'created_by' => $owner->id,
            ]);
        });

        return redirect()
            ->route('profile.edit', ['panel' => 'company'])
            ->with('toast_success', __('talenma.company_users.created'));
    }

    public function destroy(Request $request, User $member): RedirectResponse
    {
        $owner = $request->user();
        abort_unless($owner->canManageCompanyUsers(), 403);

        $profile = $owner->companyProfile;
        abort_unless($profile, 404);

        $membership = CompanyMembership::query()
            ->where('company_profile_id', $profile->id)
            ->where('user_id', $member->id)
            ->firstOrFail();

        abort_unless($member->isCompanyMember(), 403);

        DB::transaction(function () use ($member, $membership) {
            $membership->delete();
            $member->update([
                'disabled_at' => now(),
                'email' => 'disabled+'.$member->id.'.'.Str::lower(Str::random(6)).'@invalid.local',
            ]);
        });

        return redirect()
            ->route('profile.edit', ['panel' => 'company'])
            ->with('toast_success', __('talenma.company_users.removed'));
    }
}
