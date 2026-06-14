<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        if (! $user->isCompany()) {
            return redirect()->route('dashboard');
        }

        $profile = $user->companyProfile ?: $user->companyProfile()->create();

        return view('company.profile', compact('profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (! $user->isCompany()) {
            return redirect()->route('dashboard');
        }

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'sector' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'min:10'],
            'website' => ['nullable', 'url'],
            'employee_count' => ['nullable', 'string', 'max:50'],
            'hiring_needs' => ['nullable', 'string'],
        ]);

        $user->companyProfile()->updateOrCreate(['user_id' => $user->id], $data);

        return redirect()->route('company.profile.edit')->with('status', 'company-profile-updated');
    }
}
