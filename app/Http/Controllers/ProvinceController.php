<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Province;
use Inertia\Inertia;
use Inertia\Response;

class ProvinceController extends Controller
{
    public function show(Province $province): Response
    {
        $projects = Project::query()
            ->published()
            ->where('province_id', $province->id)
            ->with(['province', 'images'])
            ->latest('published_at')
            ->paginate(12)
            ->through(fn (Project $project): array => ProjectController::listCard($project));

        return Inertia::render('provinces/Show', [
            'province' => [
                'name' => $province->name,
                'slug' => $province->slug,
            ],
            'projects' => $projects,
        ]);
    }
}
