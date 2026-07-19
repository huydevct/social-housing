<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/dang-nhap', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/dang-xuat', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
