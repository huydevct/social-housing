<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'projects' => Project::count(),
                'published' => Project::whereNotNull('published_at')->count(),
                'guides' => Guide::count(),
            ],
        ]);
    }

    public function crawl(): RedirectResponse
    {
        Artisan::queue('crawl:projects', ['--source' => 'hanoi']);

        return back()->with('success', 'Đã đưa tác vụ crawl vào hàng đợi.');
    }
}
