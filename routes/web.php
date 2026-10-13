<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationShareController;

// Public Market & Landing Routes
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::match(['get', 'post'], '/lookup', [HomeController::class, 'lookup'])->name('home.lookup');
Route::get('/track/{token?}', [HomeController::class, 'track'])->name('home.track');
Route::post('/create-link', [HomeController::class, 'createLink'])->name('home.createLink');
Route::get('/api/track-data/{token}', [HomeController::class, 'trackData'])->name('home.trackData');

// Admin Authentication
Route::get('/admin/login', [DashboardController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [DashboardController::class, 'processLogin'])->name('admin.login.submit');
Route::post('/admin/logout', [DashboardController::class, 'logout'])->name('admin.logout');

// Protected Admin Dashboard & Management (Authorized Personnel Only)
Route::middleware('admin.auth')->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/links/{id}', [DashboardController::class, 'show'])->name('dashboard.show');
    Route::post('/links', [DashboardController::class, 'createLink'])->name('dashboard.createLink');
    Route::post('/links/{id}/toggle', [DashboardController::class, 'toggleLink'])->name('dashboard.toggleLink');
    Route::delete('/links/{id}', [DashboardController::class, 'deleteLink'])->name('dashboard.deleteLink');
    Route::get('/api/live-data/{id?}', [DashboardController::class, 'liveData'])->name('dashboard.liveData');
});

// Client Location Sharing Routes
Route::get('/l/{token}', [LocationShareController::class, 'show'])->name('share.show');
Route::post('/l/{token}/share', [LocationShareController::class, 'store'])->name('share.store');
