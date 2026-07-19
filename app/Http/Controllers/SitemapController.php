<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Project;
use App\Models\Province;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        $urls[] = ['loc' => route('home'), 'priority' => '1.0'];
        $urls[] = ['loc' => route('projects.index'), 'priority' => '0.9'];
        $urls[] = ['loc' => route('guides.index'), 'priority' => '0.7'];

        Project::published()->get(['slug', 'updated_at'])->each(function (Project $project) use (&$urls): void {
            $urls[] = [
                'loc' => route('projects.show', $project->slug),
                'lastmod' => $project->updated_at?->toAtomString(),
                'priority' => '0.8',
            ];
        });

        Province::whereHas('projects', fn (Builder $query) => $query->whereNotNull('published_at')->where('published_at', '<=', now()))
            ->get(['slug'])
            ->each(function (Province $province) use (&$urls): void {
                $urls[] = ['loc' => route('provinces.show', $province->slug), 'priority' => '0.6'];
            });

        Guide::published()->get(['slug', 'updated_at'])->each(function (Guide $guide) use (&$urls): void {
            $urls[] = [
                'loc' => route('guides.show', $guide->slug),
                'lastmod' => $guide->updated_at?->toAtomString(),
                'priority' => '0.5',
            ];
        });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
