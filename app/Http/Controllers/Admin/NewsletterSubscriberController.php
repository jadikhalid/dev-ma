<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterSubscriberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsletterSubscriberController extends Controller
{
    public function __construct(
        private NewsletterSubscriberService $subscribers,
    ) {}

    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());

        $list = NewsletterSubscriber::query()
            ->with(['creator', 'user'])
            ->when($q !== '', fn ($query) => $query->where('email', 'like', '%'.$q.'%'))
            ->latest('id')
            ->paginate(40)
            ->withQueryString();

        return view('admin.newsletter.subscribers', [
            'subscribers' => $list,
            'activeCount' => NewsletterSubscriber::query()->active()->count(),
            'q' => $q,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'emails' => ['required', 'string', 'max:10000'],
        ]);

        $emails = $this->subscribers->parseEmailList($data['emails']);

        if ($emails === []) {
            return back()
                ->withInput()
                ->with('toast_error', __('talenma.newsletter.subscribers_none_valid'));
        }

        $result = $this->subscribers->addMany($emails, $request->user());

        return back()->with(
            'toast_success',
            __('talenma.newsletter.subscribers_added', $result)
        );
    }

    public function destroy(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $this->subscribers->unsubscribe($subscriber);

        return back()->with('toast_success', __('talenma.newsletter.subscriber_removed'));
    }
}
