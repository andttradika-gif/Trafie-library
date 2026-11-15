<?php

use App\Http\Controllers\Tl\LiveController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'throttle:40,1'])->prefix('tl-live')->group(function () {
    Route::post('lumi', [LiveController::class, 'lumi'])->name('tl.live.lumi');
    Route::get('notifs', [LiveController::class, 'notifs'])->name('tl.live.notifs');
});