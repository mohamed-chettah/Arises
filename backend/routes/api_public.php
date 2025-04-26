<?php

use App\Http\Controllers\WaitlistController;
use Illuminate\Support\Facades\Route;

// Routes publiques sans authentification
Route::group(['throttle:5,1'], function () {
    Route::post('/waitlist', [WaitlistController::class, 'store'])->name('waitlist.store');
    Route::post('/verify-mail', [WaitlistController::class, 'verifyMail'])->name('waitlist.verifyMail');
});
