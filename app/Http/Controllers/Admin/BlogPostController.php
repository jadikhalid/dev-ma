<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Support\BlogCoverStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->with('author')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.blog.index', [
            'posts' => $posts,
        ]);
    }

    public function create(): View
    {
        return view('admin.blog.form', [
            'post' => new BlogPost([
                'locale' => app()->getLocale() === 'en' ? BlogPost::LOCALE_EN : BlogPost::LOCALE_FR,
                'status' => BlogPost::STATUS_DRAFT,
                'show_in_ticker' => true,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $slug = filled($validated['slug'] ?? null)
            ? BlogPost::uniqueSlugFrom((string) $validated['slug'])
            : BlogPost::uniqueSlugFrom($validated['title']);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = BlogCoverStorage::storeUpload($request->file('cover'));
        }

        $status = $validated['status'];
        $publishedAt = $status === BlogPost::STATUS_PUBLISHED
            ? ($validated['published_at'] ?? now())
            : null;

        $post = BlogPost::query()->create([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'body' => $this->sanitizeBody($validated['body']),
            'cover_path' => $coverPath,
            'locale' => $validated['locale'],
            'status' => $status,
            'show_in_ticker' => (bool) ($validated['show_in_ticker'] ?? false),
            'published_at' => $publishedAt,
            'created_by' => $request->user()->id,
        ]);

        $post->syncTicker();

        return redirect()
            ->route('admin.blog.index')
            ->with('toast_success', __('talenma.blog.admin.saved'));
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog.form', [
            'post' => $blogPost,
        ]);
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $validated = $this->validated($request, $blogPost);
        $previousUrl = $blogPost->tickerUrl();

        $slug = filled($validated['slug'] ?? null)
            ? BlogPost::uniqueSlugFrom((string) $validated['slug'], $blogPost->id)
            : BlogPost::uniqueSlugFrom($validated['title'], $blogPost->id);

        if ($request->hasFile('cover')) {
            BlogCoverStorage::delete($blogPost->cover_path);
            $blogPost->cover_path = BlogCoverStorage::storeUpload($request->file('cover'));
        }

        if ($request->boolean('remove_cover')) {
            BlogCoverStorage::delete($blogPost->cover_path);
            $blogPost->cover_path = null;
        }

        $status = $validated['status'];
        $publishedAt = $status === BlogPost::STATUS_PUBLISHED
            ? ($blogPost->published_at ?? now())
            : null;

        if ($status === BlogPost::STATUS_PUBLISHED && ! empty($validated['published_at'])) {
            $publishedAt = $validated['published_at'];
        }

        $blogPost->fill([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'],
            'body' => $this->sanitizeBody($validated['body']),
            'locale' => $validated['locale'],
            'status' => $status,
            'show_in_ticker' => (bool) ($validated['show_in_ticker'] ?? false),
            'published_at' => $publishedAt,
        ])->save();

        // If slug changed, drop old ticker URL then re-sync.
        if ($previousUrl !== $blogPost->tickerUrl()) {
            \App\Models\SocialFeedItem::query()
                ->where('source', 'article')
                ->where('url', $previousUrl)
                ->get()
                ->each->delete();
        }

        $blogPost->syncTicker();

        return redirect()
            ->route('admin.blog.index')
            ->with('toast_success', __('talenma.blog.admin.updated'));
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return redirect()
            ->route('admin.blog.index')
            ->with('toast_success', __('talenma.blog.admin.deleted'));
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?BlogPost $post = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:100000'],
            'locale' => ['required', Rule::in([BlogPost::LOCALE_FR, BlogPost::LOCALE_EN])],
            'status' => ['required', Rule::in([BlogPost::STATUS_DRAFT, BlogPost::STATUS_PUBLISHED])],
            'show_in_ticker' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'cover' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_cover' => ['sometimes', 'boolean'],
        ]);
    }

    private function sanitizeBody(string $body): string
    {
        $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4><a><blockquote><img>';

        return Str::of(strip_tags($body, $allowed))->trim()->toString();
    }
}
