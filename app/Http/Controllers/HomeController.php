<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'talentsCount' => User::where('role', 'dev')->where('is_subscribed', true)->count(),
            'latestArticles' => Article::published()->latest('published_at')->take(3)->get(),
        ]);
    }
}
