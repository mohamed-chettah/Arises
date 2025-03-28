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
use App\Http\Controllers\Extension\UserWebsiteController;
use App\Http\Controllers\Extension\WebsiteController;
use App\Models\User;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Google OAuth

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Cache authentification (récuperation des informations de l'utilisateur)

Route::get('/auth/status/{authKey}', function ($authKey): JsonResponse {
    $authKey = urldecode($authKey);
    $data = Cache::get("auth:$authKey");

    if (!$data) {
        return response()->json(['error' => 'Authentification expirée ou invalide.'], 404);
    }

    // Supprimer l’entrée une fois récupérée
    Cache::forget("auth:$authKey");

    return response()->json($data);
});

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/refresh', function (Request $request) {
    try {
        // On récupère manuellement le token depuis la requête (Authorization ou refresh_token)
        $refreshToken = $request->bearerToken();

        if (!$refreshToken) {
            return response()->json(['message' => 'Refresh token manquant'], 400);
        }

        $newToken = JWTAuth::setToken($refreshToken)->refresh();

        return response()->json([
            'access_token' => $newToken,
            'expires_in' => JWTAuth::factory()->getTTL() * 60, // secondes
        ]);
    } catch (TokenInvalidException $e) {
        return response()->json(['message' => 'Refresh token invalide'], 401);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Erreur lors du refresh', 'error' => $e->getMessage()], 500);
    }
});

Route::middleware(['jwt'])->group(function () {
    Route::get('/is-connected', function (Request $request) {
        return response()->json(['isConnected' => true] ,200);
    });
    Route::get('/dashboard', function () {

    });

    // Websites Routes

    Route::get('/user-website', [UserWebsiteController::class, 'index'])
        ->name('websites.store');

    Route::post('/user-website', [UserWebsiteController::class, 'store'])
        ->name('websites.store');

    Route::delete('/user-website/{id}', [UserWebsiteController::class, 'destroy'])
        ->name('websites.destroy');


//    Route::get('/logout', [LoginController::class, 'logout']);

});


// AUTH ROUTES

//
//Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
//    ->middleware('guest')
//    ->name('password.email');
//
//Route::post('/reset-password', [NewPasswordController::class, 'store'])
//    ->middleware('guest')
//    ->name('password.store');
//
//Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
//    ->middleware(['auth', 'signed', 'throttle:6,1'])
//    ->name('verification.verify');
//
//Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
//    ->middleware(['auth', 'throttle:6,1'])
//    ->name('verification.send');

//Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
//    ->middleware('auth')
//    ->name('logout');
