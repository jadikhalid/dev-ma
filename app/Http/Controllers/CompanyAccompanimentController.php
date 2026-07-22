<?php

namespace App\Http\Controllers;

use App\Services\MessagingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyAccompanimentController extends Controller
{
    public function __construct(
        private MessagingService $messaging,
    ) {}

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user?->isCompany() && $user->isApproved(), 403);

        $data = $request->validate([
            'requester_name' => ['required', 'string', 'min:2', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'min:20', 'max:5000'],
        ], [
            'requester_name.required' => __('talenma.services.accompagnement_name_required'),
            'subject.required' => __('talenma.services.accompagnement_subject_required'),
            'body.required' => __('talenma.services.accompagnement_body_required'),
            'body.min' => __('talenma.services.accompagnement_body_min'),
        ]);

        $body = trim($data['requester_name'])."\n\n".trim($data['body']);

        $conversation = $this->messaging->startStaffConversation(
            $user,
            $data['subject'],
            $body,
        );

        $message = __('talenma.services.accompagnement_sent');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'show_url' => route('inbox.show', $conversation),
            ]);
        }

        return redirect()
            ->route('services.index')
            ->with('toast_success', $message);
    }
}
