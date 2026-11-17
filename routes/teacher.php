<?php

use App\Http\Controllers\ComingSoonController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:teacher.access'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/', TeacherDashboardController::class)->name('dashboard');

        Route::get('classes', ComingSoonController::class)->defaults('feature', 'teacher-classes')->name('classes');
        Route::get('students', ComingSoonController::class)->defaults('feature', 'teacher-students')->name('students');
        Route::get('assignments', ComingSoonController::class)->defaults('feature', 'assignments')->name('assignments');
        Route::get('competitions', ComingSoonController::class)->defaults('feature', 'competitions')->name('competitions');
        Route::get('reports', ComingSoonController::class)->defaults('feature', 'reports')->name('reports');
    });
