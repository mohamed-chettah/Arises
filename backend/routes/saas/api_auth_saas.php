<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

Route::prefix('app')->middleware(['throttle:20,1'])->group(function () {
    
    Route::post('/auth/refresh', function (Request $request) {
        try {
            // Récupération du refresh token depuis la requête
            $refreshToken = $request->bearerToken() ?? $request->input('refresh_token');
            
            if (!$refreshToken) {
                return response()->json([
                    'message' => 'Refresh token missing'
                ], 400);
            }

            // Refresh de l'access token (invalide automatiquement l'ancien)
            $newAccessToken = JWTAuth::setToken($refreshToken)->refresh();
            
            // Génération d'un nouveau refresh token (rotation)
            $user = JWTAuth::setToken($newAccessToken)->authenticate();
            $newRefreshToken = JWTAuth::customClaims([
                'type' => 'refresh',
                'exp' => now()->addMinutes(config('jwt.refresh_ttl'))->timestamp
            ])->fromUser($user);

            return response()->json([
                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
                'expires_in' => JWTAuth::factory()->getTTL() * 60, // secondes
                'token_type' => 'Bearer'
            ]);

        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => 'Invalid or expired refresh token'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token refresh failed',
                'error' => $e->getMessage()
            ], 500);
        }
    });
}); 