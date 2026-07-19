<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuideRequest;
use App\Models\Guide;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GuideController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/guides/Index', [
            'guides' => Guide::query()
                ->orderBy('sort')
                ->get(['id', 'title', 'slug', 'published_at'])
                ->map(fn (Guide $guide): array => [
                    'id' => $guide->id,
                    'title' => $guide->title,
                    'slug' => $guide->slug,
                    'published' => $guide->published_at !== null,
                ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/guides/Form');
    }

    public function store(StoreGuideRequest $request): RedirectResponse
    {
        Guide::create($this->payload($request));

        return redirect()->route('admin.guides.index')->with('success', 'Đã tạo bài hướng dẫn.');
    }

    public function edit(Guide $guide): Response
    {
        return Inertia::render('admin/guides/Form', [
            'guide' => $guide,
        ]);
    }

    public function update(StoreGuideRequest $request, Guide $guide): RedirectResponse
    {
        $guide->update($this->payload($request));

        return redirect()->route('admin.guides.index')->with('success', 'Đã cập nhật bài hướng dẫn.');
    }

    public function destroy(Guide $guide): RedirectResponse
    {
        $guide->delete();

        return redirect()->route('admin.guides.index')->with('success', 'Đã xóa bài hướng dẫn.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(StoreGuideRequest $request): array
    {
        $data = $request->safe()->except('published');
        $data['published_at'] = $request->boolean('published') ? now() : null;

        return $data;
    }
}
