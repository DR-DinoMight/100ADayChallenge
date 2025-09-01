<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\TaskTracker;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WidgetController;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/magic-link/request', [AuthController::class, 'requestMagicLink'])->name('magic-link.request');
Route::get('/magic-link/verify/{token}', [AuthController::class, 'verifyMagicLink'])->name('magic-link.verify');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (require authentication)
Route::middleware('auth.magic')->group(function () {
    Route::get('/tracker', TaskTracker::class)->name('task-tracker');

    Route::get('/widget-demo', function () {
        return view('widget-demo');
    })->name('widget-demo');
});

// Widget endpoint for iframe embedding (public)
Route::get('/widget', [WidgetController::class, 'show'])->name('widget');
