<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        abort_unless($user?->isCompanyOwner() && $user->isApproved(), 403);

        $services = Service::active()->get();

        return view('services.company-index', compact('services'));
    }
}
