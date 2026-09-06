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

    public function unsubscribe(string $token): View
    {
        $subscriber = $this->subscribers->unsubscribeByToken($token);

        return view('newsletter.unsubscribed', [
            'found' => $subscriber !== null,
        ]);
    }
}
