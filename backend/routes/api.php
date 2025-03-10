<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TwitterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

//
Route::get('/auth/twitter', [TwitterController::class, 'redirectToTwitter']);
Route::get('/auth/twitter/callback', [TwitterController::class, 'handleTwitterCallback']);

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/test-session', function () {
    $count = session()->get('count', 0);
    $count++;
    session()->put('count', $count);
    return "Session count: $count";
});
Route::get('/auth/status/{authKey}', function ($authKey): JsonResponse {
    $data = Cache::get("auth:$authKey");

    if (!$data) {
        return response()->json(['error' => 'Authentification expirée ou invalide.'], 404);
    }

    // Supprimer l’entrée une fois récupérée
    Cache::forget("auth:$authKey");

    return response()->json($data);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {

    });
    Route::get('/logout', [LoginController::class, 'logout']);
});


Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
