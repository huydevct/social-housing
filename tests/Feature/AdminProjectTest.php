<?php

use App\Models\Project;
use App\Models\Province;
use App\Models\User;

it('blocks guests from the admin area', function () {
    $this->get('/admin')->assertRedirect('/dang-nhap');
});

it('blocks non-admin users from the admin area', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)->get('/admin')->assertForbidden();
});

it('lets an admin view the dashboard', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->get('/admin')->assertOk();
});

it('lets an admin create a project', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $province = Province::factory()->create();

    $this->actingAs($admin)->post('/admin/projects', [
        'name' => 'Dự án mới',
        'slug' => 'du-an-moi',
        'province_id' => $province->id,
        'status' => 'receiving',
        'published' => true,
    ])->assertRedirect('/admin/projects');

    $this->assertDatabaseHas('projects', ['slug' => 'du-an-moi']);
    expect(Project::where('slug', 'du-an-moi')->first()->published_at)->not->toBeNull();
});

it('toggles project publish state', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $project = Project::factory()->draft()->create();

    $this->actingAs($admin)->post("/admin/projects/{$project->slug}/publish")->assertRedirect();

    expect($project->fresh()->published_at)->not->toBeNull();
});
