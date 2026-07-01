<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isStaff()) {
            return redirect()->route('admin.users.index');
        }

        if ($user->isCompany()) {
            $user->load('companyProfile');
            $recentRequests = $user->recruitmentRequests()->with('talent.profile')->latest()->take(5)->get();

            return view('dashboard.company', compact('recentRequests'));
        }

        $user->load('profile');

        return view('dashboard.talent');
    }
}
