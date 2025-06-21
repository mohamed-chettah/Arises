<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['jwt','throttle:20,1'])->group(function () {
    Route::get('/is-connected', function (Request $request) {
        return response()->json(['isConnected' => true] ,200);
    });
    Route::get('/dashboard', function () {

    });
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::post('/user', [AuthenticatedSessionController::class, 'edit']);
});
