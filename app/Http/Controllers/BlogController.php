<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale() === 'en' ? BlogPost::LOCALE_EN : BlogPost::LOCALE_FR;

        $posts = BlogPost::query()
            ->published()
            ->forLocale($locale)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9);

        return view('blog.index', [
            'posts' => $posts,
        ]);
    }

    public function show(string $slug): View
    {
        $locale = app()->getLocale() === 'en' ? BlogPost::LOCALE_EN : BlogPost::LOCALE_FR;

        $post = BlogPost::query()
            ->published()
            ->forLocale($locale)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('blog.show', [
            'post' => $post,
        ]);
    }
}
