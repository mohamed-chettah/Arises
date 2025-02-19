<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwitterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function () {
    return response()->json(['message' => 'Hello World!']);
});

Route::get('/auth/twitter', [TwitterController::class, 'redirectToTwitter']);
Route::get('/auth/twitter/callback', [TwitterController::class, 'handleTwitterCallback']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {

    });
    Route::get('/logout', [LoginController::class, 'logout']);
});

