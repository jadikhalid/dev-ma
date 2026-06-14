<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileDetailsController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        if (! $user->isTalent()) {
            return redirect()->route('dashboard');
        }

        $profile = $user->profile ?: $user->profile()->create();

        return view('talent.profile', compact('profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (! $user->isTalent()) {
            return redirect()->route('dashboard');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'bio' => ['required', 'string', 'min:10'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:50'],
            'daily_rate_eur' => ['required', 'integer', 'min:10'],
            'availability' => ['required', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'skills' => ['nullable', 'string'],
            'github_url' => ['nullable', 'url'],
            'linkedin_url' => ['nullable', 'url'],
            'portfolio_url' => ['nullable', 'url'],
        ]);

        if (! empty($data['skills'])) {
            $data['skills'] = array_map('trim', explode(',', $data['skills']));
        } else {
            $data['skills'] = [];
        }

        $user->profile()->updateOrCreate(['user_id' => $user->id], $data);

        return redirect()->route('profile.details.edit')->with('status', 'profile-updated');
    }
}
