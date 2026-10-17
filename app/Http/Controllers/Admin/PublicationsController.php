<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialFeedItem;
use App\Support\SocialFeedStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicationsController extends Controller
{
    public function index(): View
    {
        SocialFeedItem::pruneExcess();

        return view('admin.publications', [
            'newsItems' => SocialFeedItem::forNewsTicker(),
            'newsMaxItems' => SocialFeedItem::MAX_ITEMS,
        ]);
    }

    public function storeNews(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('talenma.admin.news.saved'),
            ]);
        }

        return redirect()
            ->to(route('admin.publications.index').'#actualites')
            ->with('news_saved', true);
    }

    public function updateNews(Request $request, SocialFeedItem $newsItem): RedirectResponse|JsonResponse
    {
        abort_unless($newsItem->source === 'article', 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('talenma.admin.news.updated'),
            ]);
        }

        return redirect()
            ->to(route('admin.publications.index').'#actualites')
            ->with('news_updated', true);
    }

    public function destroyNews(Request $request, SocialFeedItem $newsItem): RedirectResponse|JsonResponse
    {
        $newsItem->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('talenma.admin.news.deleted'),
            ]);
        }

        return redirect()
            ->to(route('admin.publications.index').'#actualites')
            ->with('news_deleted', true);
    }
}
