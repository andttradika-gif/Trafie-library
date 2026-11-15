<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return Auth::user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('student.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/akun', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/akun/nama', [AccountController::class, 'updateName'])->name('account.name');
    Route::put('/akun/password', [AccountController::class, 'updatePassword'])->name('account.password');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        require base_path('routes/admin_extra.php');
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    Route::get('/categories', [Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [Admin\CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/books', [Admin\BookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [Admin\BookController::class, 'create'])->name('books.create');
    Route::post('/books', [Admin\BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [Admin\BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [Admin\BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [Admin\BookController::class, 'destroy'])->name('books.destroy');

    Route::get('/loans', [Admin\LoanController::class, 'index'])->name('loans.index');
    Route::patch('/loans/{loan}/approve', [Admin\LoanController::class, 'approve'])->name('loans.approve');
    Route::patch('/loans/{loan}/reject', [Admin\LoanController::class, 'reject'])->name('loans.reject');
    Route::patch('/loans/{loan}/return', [Admin\LoanController::class, 'returnBook'])->name('loans.return');
    Route::patch('/loans/{loan}/fine-paid', [Admin\LoanController::class, 'finePaid'])->name('loans.finepaid');

    Route::get('/renewals', [Admin\RenewalController::class, 'index'])->name('renewals.index');
    Route::patch('/renewals/{renewal}/approve', [Admin\RenewalController::class, 'approve'])->name('renewals.approve');
    Route::patch('/renewals/{renewal}/reject', [Admin\RenewalController::class, 'reject'])->name('renewals.reject');

    Route::get('/reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/students', [Admin\StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [Admin\StudentController::class, 'show'])->name('students.show');
    Route::patch('/students/{student}/reset-password', [Admin\StudentController::class, 'resetPassword'])->name('students.reset');

    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [Admin\ReportController::class, 'export'])->name('reports.export');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::view('/dashboard', 'student.dashboard')->name('dashboard');

    Route::get('/books', [Student\BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [Student\BookController::class, 'show'])->name('books.show');
    Route::post('/books/{book}/review', [Student\ReviewController::class, 'store'])->name('reviews.store');

    Route::post('/books/{book}/pinjam', [Student\LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans', [Student\LoanController::class, 'index'])->name('loans.index');
    Route::patch('/loans/{loan}/cancel', [Student\LoanController::class, 'cancel'])->name('loans.cancel');
    Route::post('/loans/{loan}/renew', [Student\RenewalController::class, 'store'])->name('loans.renew');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/books-lookup', [\App\Http\Controllers\Admin\BookController::class, 'lookup'])->middleware('throttle:30,1')->name('books.lookup');
});

require __DIR__ . '/auth.php';

// V2-15: landing page Trafie-library (didaftarkan terakhir, jadi menimpa route '/' lama)
Route::get('/', [\App\Http\Controllers\LandingV15Controller::class, 'index'])->name('landing.v15');

require __DIR__ . '/trafie.php';
require __DIR__ . '/student-extra.php';
require __DIR__ . '/reading.php';

require base_path('routes/tl_live.php');
require __DIR__ . '/rating.php';
require __DIR__ . '/admin-more.php';
