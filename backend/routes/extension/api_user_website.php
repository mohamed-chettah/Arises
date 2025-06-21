<?php

use App\Http\Controllers\Extension\UserWebsiteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['jwt','throttle:20,1'])->group(function () {
    Route::get('/user-website', [UserWebsiteController::class, 'index'])
        ->name('websites.store');

    Route::post('/user-website', [UserWebsiteController::class, 'store'])
        ->name('websites.store');

    Route::delete('/user-website/{id}', [UserWebsiteController::class, 'destroy'])
        ->name('websites.destroy');
});
