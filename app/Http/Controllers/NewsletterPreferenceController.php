<?php

namespace App\Http\Controllers;

use App\Services\NewsletterSubscriberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterPreferenceController extends Controller
{
    public function __construct(
        private NewsletterSubscriberService $subscribers,
    ) {}

    public function subscribe(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $this->subscribers->subscribe($data['email']);

        return back()->with('toast_success', __('talenma.newsletter.subscribe_success'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && ($user->isTalent() || $user->isCompany()) && $user->isApproved(), 403);

        $data = $request->validate([
            'newsletter_opt_in' => ['required', 'boolean'],
        ]);

        $this->subscribers->syncUserPreference($user, (bool) $data['newsletter_opt_in']);

        return back()->with('toast_success', __('talenma.newsletter.preference_saved'));
    }

    public function unsubscribe(string $token): View
    {
        $subscriber = $this->subscribers->unsubscribeByToken($token);

        return view('newsletter.unsubscribed', [
            'found' => $subscriber !== null,
        ]);
    }
}
