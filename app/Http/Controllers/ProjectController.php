<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\Province;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['province', 'status', 'keyword']);

        $projects = Project::query()
            ->published()
            ->with(['province', 'images'])
            ->when($filters['province'] ?? null, fn ($query, $slug) => $query->whereHas('province', fn ($q) => $q->where('slug', $slug)))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['keyword'] ?? null, fn ($query, $keyword) => $query->where('name', 'like', "%{$keyword}%"))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Project $project): array => self::listCard($project));

        return Inertia::render('projects/Index', [
            'projects' => $projects,
            'filters' => $filters,
            'provinces' => Province::orderBy('name')->get(['name', 'slug']),
            'statuses' => ProjectStatus::options(),
        ]);
    }

    public function show(Project $project): Response
    {
        abort_if($project->published_at === null, 404);

        $project->load(['province', 'investor', 'images']);

        return Inertia::render('projects/Show', [
            'project' => self::detail($project),
        ]);
    }

    /**
     * Compact shape for listing cards.
     *
     * @return array<string, mixed>
     */
    public static function listCard(Project $project): array
    {
        return [
            'name' => $project->name,
            'slug' => $project->slug,
            'district' => $project->district,
            'address' => $project->address,
            'province' => $project->province?->name,
            'province_slug' => $project->province?->slug,
            'status' => $project->status->value,
            'status_label' => $project->status->label(),
            'price_from' => $project->price_from,
            'price_to' => $project->price_to,
            'area_from' => $project->area_from,
            'area_to' => $project->area_to,
            'application_end_at' => $project->application_end_at?->toIso8601String(),
            'cover' => $project->coverImage()?->url,
        ];
    }

    /**
     * Full shape for the detail page.
     *
     * @return array<string, mixed>
     */
    public static function detail(Project $project): array
    {
        return [
            ...self::listCard($project),
            'former_address' => $project->former_address,
            'total_units' => $project->total_units,
            'description' => $project->description,
            'application_guide' => $project->application_guide,
            'application_start_at' => $project->application_start_at?->toIso8601String(),
            'investor' => $project->investor ? [
                'name' => $project->investor->name,
                'website' => $project->investor->website,
                'phone' => $project->investor->phone,
            ] : null,
            'images' => $project->images->map(fn ($image): array => ['url' => $image->url])->all(),
            'source_name' => $project->source_name,
            'source_url' => $project->source_url,
            'meta_title' => $project->meta_title,
            'meta_description' => $project->meta_description,
        ];
    }
}
