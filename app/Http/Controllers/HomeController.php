<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Project;
use App\Models\Province;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $featured = Project::query()
            ->published()
            ->with(['province', 'images'])
            ->latest('published_at')
            ->take(6)
            ->get()
            ->map(fn (Project $project): array => ProjectController::listCard($project));

        $provinces = Province::query()
            ->withCount(['projects' => fn (Builder $query) => $query->whereNotNull('published_at')->where('published_at', '<=', now())])
            ->whereHas('projects', fn (Builder $query) => $query->whereNotNull('published_at')->where('published_at', '<=', now()))
            ->orderByDesc('projects_count')
            ->take(12)
            ->get(['id', 'name', 'slug'])
            ->map(fn (Province $province): array => [
                'name' => $province->name,
                'slug' => $province->slug,
                'projects_count' => $province->projects_count,
            ]);

        $guides = Guide::query()
            ->published()
            ->orderBy('sort')
            ->take(3)
            ->get(['title', 'slug', 'excerpt']);

        return Inertia::render('Home', [
            'featured' => $featured,
            'provinces' => $provinces,
            'guides' => $guides,
        ]);
    }
}
