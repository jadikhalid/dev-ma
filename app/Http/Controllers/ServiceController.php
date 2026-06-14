<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::active()->get();

        return view('services.index', compact('services'));
    }

    public function show(string $slug)
    {
        $service = Service::active()->where('slug', $slug)->firstOrFail();

        return view('services.show', compact('service'));
    }
}
