<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


Route::group(['throttle:20,1'], function () {
    // Google OAuth
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

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
                return response()->json(['message' => 'Refresh token missing'], 400);
            }

            $newToken = JWTAuth::setToken($refreshToken)->refresh();

            return response()->json([
                'access_token' => $newToken,
                'expires_in' => JWTAuth::factory()->getTTL() * 60, // secondes
            ]);
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error at refresh ²   A1q>', 'error' => $e->getMessage()], 500);
        }
    });


});
