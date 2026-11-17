<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\TeacherRequestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ComingSoonController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:admin.access'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::middleware('can:users.manage')->group(function () {
            Route::get('users', [UserController::class, 'index'])->name('users.index');
        });

        Route::middleware('can:teachers.approve')->group(function () {
            Route::get('teacher-requests', [TeacherRequestController::class, 'index'])->name('teacher-requests.index');
            Route::post('teacher-requests/{user}/approve', [TeacherRequestController::class, 'approve'])->name('teacher-requests.approve');
            Route::post('teacher-requests/{user}/reject', [TeacherRequestController::class, 'reject'])->name('teacher-requests.reject');
        });

        Route::get('courses', ComingSoonController::class)->defaults('feature', 'admin-courses')->name('courses');
        Route::get('lessons', ComingSoonController::class)->defaults('feature', 'admin-lessons')->name('lessons');
        Route::get('classes', ComingSoonController::class)->defaults('feature', 'admin-classes')->name('classes');
        Route::get('challenges', ComingSoonController::class)->defaults('feature', 'admin-challenges')->name('challenges');
        Route::get('achievements', ComingSoonController::class)->defaults('feature', 'admin-achievements')->name('achievements');
        Route::get('analytics', ComingSoonController::class)->defaults('feature', 'admin-analytics')->name('analytics');
        Route::get('settings', ComingSoonController::class)->defaults('feature', 'admin-settings')->name('settings');
    });
