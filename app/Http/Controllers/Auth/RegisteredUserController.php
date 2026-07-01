<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $role = request('role');
        $defaultRole = in_array($role, ['dev', 'company'], true) ? $role : 'dev';

        return view('auth.register', [
            'defaultRole' => $defaultRole,
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'approval_status' => $validated['role'] === 'dev' ? User::APPROVAL_PENDING : User::APPROVAL_APPROVED,
            'approved_at' => $validated['role'] === 'company' ? now() : null,
        ]);

        if ($user->role === 'company') {
            $user->companyProfile()->create([
                'country' => 'France',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        $request->clearRateLimiter();

        return redirect(route('verification.notice', absolute: false));
    }
}
