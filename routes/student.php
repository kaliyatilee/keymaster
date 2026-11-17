<?php

use App\Http\Controllers\ComingSoonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student area
|--------------------------------------------------------------------------
|
| Features scheduled for later phases render an honest "coming soon" page
| so navigation works end to end without pretending the feature exists.
|
*/

Route::middleware(['auth', 'verified', 'can:practice.use'])->group(function () {
    Route::get('practice', ComingSoonController::class)
        ->defaults('feature', 'practice')->name('practice');
    Route::get('challenges', ComingSoonController::class)
        ->defaults('feature', 'challenges')->name('challenges');
    Route::get('classes', ComingSoonController::class)
        ->defaults('feature', 'student-classes')->name('classes');
});
