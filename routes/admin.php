<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/projects/{project}/publish', [ProjectController::class, 'togglePublish'])->name('projects.publish');
    Route::resource('projects', ProjectController::class)->except('show');

    Route::resource('guides', GuideController::class)->except('show');

    Route::post('/crawl', [DashboardController::class, 'crawl'])->name('crawl');
});
