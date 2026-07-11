<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialFeedItem;
use App\Models\SocialPost;
use App\Support\SocialFeedStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicationsController extends Controller
{
    public function index(): View
    {
        SocialFeedItem::pruneExcess();

        return view('admin.publications', [
            'newsItems' => SocialFeedItem::forNewsTicker(),
            'newsMaxItems' => SocialFeedItem::MAX_ITEMS,
            'socialPosts' => SocialPost::forSlider(),
            'socialMaxItems' => SocialPost::MAX_ITEMS,
            'networks' => SocialPost::NETWORKS,
        ]);
    }

    public function storeNews(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = SocialFeedStorage::storeUpload($request->file('thumbnail'));
        }

        SocialFeedItem::pushItem([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'url' => $validated['url'],
            'source' => 'article',
            'thumbnail' => $thumbnailPath,
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->to(route('admin.publications.index').'#actualites')
            ->with('news_saved', true);
    }

    public function updateNews(Request $request, SocialFeedItem $newsItem): RedirectResponse
    {
        abort_unless($newsItem->source === 'article', 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('thumbnail')) {
            SocialFeedStorage::delete($newsItem->thumbnail);
            $newsItem->thumbnail = SocialFeedStorage::storeUpload($request->file('thumbnail'));
        }

        $newsItem->fill([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'url' => $validated['url'],
            'source' => 'article',
        ])->save();

        return redirect()
            ->to(route('admin.publications.index').'#actualites')
            ->with('news_updated', true);
    }

    public function destroyNews(SocialFeedItem $newsItem): RedirectResponse
    {
        $newsItem->delete();

        return redirect()
            ->to(route('admin.publications.index').'#actualites')
            ->with('news_deleted', true);
    }

    public function storeSocialPost(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'post_title' => ['required', 'string', 'max:255'],
            'post_subtitle' => ['required', 'string', 'max:255'],
            'post_url' => ['required', 'url', 'max:2048'],
            'post_network' => ['required', Rule::in(SocialPost::NETWORKS)],
            'post_thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        $thumbnailPath = null;

        if ($request->hasFile('post_thumbnail')) {
            $thumbnailPath = SocialFeedStorage::storeUpload($request->file('post_thumbnail'));
        }

        SocialPost::pushPost([
            'title' => $validated['post_title'],
            'subtitle' => $validated['post_subtitle'],
            'url' => $validated['post_url'],
            'network' => $validated['post_network'],
            'thumbnail' => $thumbnailPath,
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->to(route('admin.publications.index').'#reseaux')
            ->with('post_saved', true);
    }

    public function destroySocialPost(SocialPost $socialPost): RedirectResponse
    {
        $socialPost->delete();

        return redirect()
            ->to(route('admin.publications.index').'#reseaux')
            ->with('post_deleted', true);
    }
}
