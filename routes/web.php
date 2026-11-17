<?php

use App\Http\Controllers\Auth\ParentalConsentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Public\PageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');

Route::name('pages.')->group(function () {
    Route::get('about', [PageController::class, 'about'])->name('about');
    Route::get('courses', [PageController::class, 'courses'])->name('courses');
    Route::get('typing-test', [PageController::class, 'typingTest'])->name('typing-test');
    Route::get('leaderboard', [PageController::class, 'leaderboard'])->name('leaderboard');
    Route::get('privacy', [PageController::class, 'privacy'])->name('privacy');
    Route::get('terms', [PageController::class, 'terms'])->name('terms');
});

/*
|--------------------------------------------------------------------------
| Parental consent
|--------------------------------------------------------------------------
|
| The guardian's link works without signing in; the token is single-use
| and stored only as a SHA-256 hash.
|
*/

Route::get('consent/{token}', [ParentalConsentController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{64}')
    ->name('consent.show');
Route::post('consent/{token}', [ParentalConsentController::class, 'respond'])
    ->where('token', '[A-Za-z0-9]{64}')
    ->middleware('throttle:10,1')
    ->name('consent.respond');

Route::middleware('auth')->group(function () {
    Route::get('account/pending', [ParentalConsentController::class, 'pending'])->name('consent.pending');
    Route::post('account/pending/resend', [ParentalConsentController::class, 'resend'])
        ->middleware('throttle:3,60')
        ->name('consent.resend');
});

/*
|--------------------------------------------------------------------------
| Home after sign-in: sends each role to its own dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

require __DIR__.'/settings.php';
