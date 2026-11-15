<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Student;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/reading', [Student\ReadingController::class, 'index'])->name('reading.index');
    Route::post('/reading', [Student\ReadingController::class, 'store'])->name('reading.store');
    Route::patch('/reading/{progress}', [Student\ReadingController::class, 'update'])->name('reading.update');
    Route::post('/reading/{progress}/selesai', [Student\ReadingController::class, 'complete'])->name('reading.complete');

    Route::get('/achievements', [Student\AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/challenges', [Student\ChallengeController::class, 'index'])->name('challenges.index');
    Route::post('/challenges/{challenge}/ikut', [Student\ChallengeController::class, 'join'])->name('challenges.join');
    Route::get('/galaxy', [Student\GalaxyController::class, 'index'])->name('galaxy.index');
    Route::get('/wrapped', [Student\WrappedController::class, 'index'])->name('wrapped.index');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/penyelesaian', [Admin\CompletionController::class, 'index'])->name('completions.index');
    Route::patch('/penyelesaian/{completion}/batalkan', [Admin\CompletionController::class, 'revoke'])->name('completions.revoke');
});