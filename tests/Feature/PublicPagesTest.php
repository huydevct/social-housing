<?php

use App\Models\Guide;
use App\Models\Project;
use App\Models\Province;

it('shows the home page', function () {
    Project::factory()->create();

    $this->get('/')->assertOk();
});

it('lists published projects and hides drafts', function () {
    $published = Project::factory()->create(['name' => 'Dự án công khai']);
    Project::factory()->draft()->create(['name' => 'Dự án nháp']);

    $this->get('/du-an')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('projects/Index')
            ->has('projects.data', 1)
        );
});

it('filters projects by province', function () {
    $hanoi = Province::factory()->create(['name' => 'Hà Nội', 'slug' => 'ha-noi']);
    $other = Province::factory()->create();
    Project::factory()->create(['province_id' => $hanoi->id]);
    Project::factory()->create(['province_id' => $other->id]);

    $this->get('/du-an?province=ha-noi')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('projects.data', 1));
});

it('shows a published project detail', function () {
    $project = Project::factory()->create(['slug' => 'du-an-abc']);

    $this->get('/du-an/du-an-abc')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('projects/Show'));
});

it('returns 404 for a draft project detail', function () {
    Project::factory()->draft()->create(['slug' => 'du-an-nhap']);

    $this->get('/du-an/du-an-nhap')->assertNotFound();
});

it('renders the sitemap', function () {
    Project::factory()->create();
    Guide::factory()->create();

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml');
});
