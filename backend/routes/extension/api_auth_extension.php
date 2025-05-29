<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\Extension\GoogleExtensionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;


Route::group(['throttle:20,1'], function () {
    // Google OAuth
    Route::get('/auth/google', [GoogleExtensionController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [GoogleExtensionController::class, 'handleGoogleCallback']);

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
