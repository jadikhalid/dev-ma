<?php

namespace App\Http\Controllers;

use App\Models\Article;

class MagazineController extends Controller
{
    public function index()
    {
        $articles = Article::published()->latest('published_at')->paginate(9);

        return view('magazine.index', compact('articles'));
    }

    public function show(string $slug)
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();
        $related = Article::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('magazine.show', compact('article', 'related'));
    }
}
