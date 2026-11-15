<?php

use App\Http\Controllers\Student;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:student', 'throttle:30,1'])->prefix('student')->name('student.')->group(function () {
    Route::post('/books/{book}/rating', [Student\RatingController::class, 'store'])->name('rate.store');
    Route::delete('/books/{book}/rating', [Student\RatingController::class, 'destroy'])->name('rate.destroy');
});