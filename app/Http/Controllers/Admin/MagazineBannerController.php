<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MagazineBannerItem;
use App\Support\MagazineBannerStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MagazineBannerController extends Controller
{
    public function index(): View
    {
        return view('admin.magazine-banner', [
            'items' => MagazineBannerItem::forBanner(),
            'maxItems' => MagazineBannerItem::MAX_ITEMS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = MagazineBannerStorage::storeUpload($request->file('thumbnail'));
        }

        MagazineBannerItem::pushItem([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'url' => $validated['url'],
            'thumbnail' => $thumbnailPath,
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.magazine-banner.index')
            ->with('banner_saved', true);
    }

    public function destroy(MagazineBannerItem $magazineBannerItem): RedirectResponse
    {
        $magazineBannerItem->delete();

        return redirect()
            ->route('admin.magazine-banner.index')
            ->with('banner_deleted', true);
    }
}
