<?php

use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/du-an', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/du-an/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/nha-o-xa-hoi/{province:slug}', [ProvinceController::class, 'show'])->name('provinces.show');

Route::get('/huong-dan', [GuideController::class, 'index'])->name('guides.index');
Route::get('/huong-dan/{guide:slug}', [GuideController::class, 'show'])->name('guides.show');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
