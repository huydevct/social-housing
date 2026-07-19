<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Investor;
use App\Models\Project;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/projects/Index', [
            'projects' => Project::query()
                ->with('province')
                ->latest()
                ->paginate(20)
                ->through(fn (Project $project): array => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'slug' => $project->slug,
                    'province' => $project->province?->name,
                    'status_label' => $project->status->label(),
                    'published' => $project->published_at !== null,
                ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/projects/Form', $this->formOptions());
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Project::create($this->payload($request));

        return redirect()->route('admin.projects.index')->with('success', 'Đã tạo dự án.');
    }

    public function edit(Project $project): Response
    {
        return Inertia::render('admin/projects/Form', [
            ...$this->formOptions(),
            'project' => $project,
        ]);
    }

    public function update(StoreProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($this->payload($request));

        return redirect()->route('admin.projects.index')->with('success', 'Đã cập nhật dự án.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Đã xóa dự án.');
    }

    public function togglePublish(Project $project): RedirectResponse
    {
        $project->update([
            'published_at' => $project->published_at === null ? now() : null,
        ]);

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(StoreProjectRequest $request): array
    {
        $data = $request->safe()->except('published');
        $data['published_at'] = $request->boolean('published') ? now() : null;

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'provinces' => Province::orderBy('name')->get(['id', 'name']),
            'investors' => Investor::orderBy('name')->get(['id', 'name']),
            'statuses' => ProjectStatus::options(),
        ];
    }
}
