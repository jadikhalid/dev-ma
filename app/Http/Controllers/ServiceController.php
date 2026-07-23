<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::active()->get();

        if ($this->useAppShell()) {
            return view('services.company-index', compact('services'));
        }

        return view('services.index', compact('services'));
    }

    public function show(string $slug): View|RedirectResponse
    {
        if ($this->useAppShell()) {
            return redirect()->route('services.index');
        }

        $service = Service::active()->where('slug', $slug)->firstOrFail();

        return view('services.show', compact('service'));
    }

    private function useAppShell(): bool
    {
        $user = Auth::user();

        return $user && $user->isCompanyOwner() && $user->isApproved();
    }
}
