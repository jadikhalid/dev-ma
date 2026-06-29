<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use App\Models\User;
use Illuminate\Http\Request;

class CompanySearchController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        $query = User::where('role', 'dev')
            ->where('is_subscribed', true)
            ->where('subscription_expires_at', '>', now())
            ->with('profile')
            ->whereHas('profile', fn ($q) => $q->whereNotNull('title')->whereNotNull('bio'));

        if ($request->filled('city')) {
            $query->whereHas('profile', fn ($q) => $q->where('city', $request->city));
        }

        if ($request->filled('country')) {
            $query->whereHas('profile', fn ($q) => $q->where('country', $request->country));
        }

        if ($request->filled('profession')) {
            $profession = Profession::query()
                ->where('slug', $request->profession)
                ->where('is_active', true)
                ->first();

            if ($profession) {
                $name = $profession->localizedName();
                $query->whereHas('profile', fn ($q) => $q->where('title', 'like', '%'.$name.'%'));
            }
        }

        if ($request->filled('keyword')) {
            $keyword = str_replace(['%', '_'], ['\\%', '\\_'], $request->keyword);
            $query->whereHas('profile', function ($q) use ($keyword) {
                $q->where(function ($subQ) use ($keyword) {
                    $subQ->where('title', 'like', '%'.$keyword.'%')
                        ->orWhere('bio', 'like', '%'.$keyword.'%');
                });
            });
        }

        $talents = $query->latest()->paginate(12)->withQueryString();

        return view('company.search', compact('talents'));
    }

    public function show(Request $request, User $talent)
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        if ($talent->role !== 'dev' || ! $talent->hasActiveSubscription()) {
            abort(404);
        }

        $talent->load('profile');

        return view('company.talent-show', compact('talent'));
    }
}
