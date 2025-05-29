<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;


Route::prefix('app')->middleware(['throttle:20,1'])->group(function () {
    // Google OAuth
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

    // Cache authentification (récuperation des informations de l'utilisateur)

    Route::get('/auth/status/{authKey}', function ($authKey): JsonResponse {

        $cacheKey = "auth:" . urldecode($authKey);
        $data = Cache::get($cacheKey);

        if (!$data) {
            return response()->json(['error' => 'Authentification expirée ou invalide.'], 404);
        }

        Cache::forget($cacheKey);

        return response()->json($data);
    });
});
