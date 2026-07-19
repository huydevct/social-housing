<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use Inertia\Inertia;
use Inertia\Response;

class GuideController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('guides/Index', [
            'guides' => Guide::query()
                ->published()
                ->orderBy('sort')
                ->get(['title', 'slug', 'excerpt']),
        ]);
    }

    public function show(Guide $guide): Response
    {
        abort_if($guide->published_at === null, 404);

        return Inertia::render('guides/Show', [
            'guide' => [
                'title' => $guide->title,
                'slug' => $guide->slug,
                'excerpt' => $guide->excerpt,
                'body' => $guide->body,
                'meta_title' => $guide->meta_title,
                'meta_description' => $guide->meta_description,
                'updated_at' => $guide->updated_at?->toIso8601String(),
            ],
        ]);
    }
}
