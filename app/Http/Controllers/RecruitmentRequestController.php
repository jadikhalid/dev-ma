<?php

namespace App\Http\Controllers;

use App\Models\RecruitmentRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RecruitmentRequestController extends Controller
{
    public function create(Request $request, ?User $talent = null)
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        return view('recruitment.create', [
            'talent' => $talent?->load('profile'),
            'mode' => $request->query('mode', $talent ? 'intermediary' : 'intermediary'),
        ]);
    }

    public function store(Request $request)
    {
        if (! $request->user()->isCompany()) {
            return redirect()->route('dashboard');
        }

        $data = $request->validate([
            'developer_user_id' => ['nullable', 'exists:users,id'],
            'mode' => ['required', 'in:direct,intermediary'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:20'],
        ]);

        RecruitmentRequest::create([
            'company_user_id' => $request->user()->id,
            'developer_user_id' => $data['developer_user_id'] ?? null,
            'mode' => $data['mode'],
            'subject' => $data['subject'],
            'message' => $data['message'],
        ]);

        return redirect()->route('dashboard')->with('recruitment_sent', __('talenma.dashboard.company.request_sent'));
    }
}
