<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Extension\UserWebsiteController;
use App\Http\Controllers\WaitlistController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;


Route::group(['throttle:10,1'], function () {
    Route::post('/waitlist', [WaitlistController::class, 'store'])->name('waitlist.store');
//    Route::post('/mail-waitlist', [WaitlistController::class, 'store'])->name('waitlist.store');
});

Route::group(['throttle:20,1'], function () {
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

        try {
            DB::beginTransaction();


            Cache::forget("auth:$authKey");
            DB::commit();
            return response()->json($data);
        } catch (\Exception $e) {
            DB::rollBack(); // On nettoie la transaction avant de renvoyer l'erreur
            // Vous pouvez logger l'erreur ou renvoyer une réponse d'erreur appropriée
            return response()->json(['error' => $e->getMessage()], 500);
        }

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
});

Route::middleware(['jwt','throttle:20,1'])->group(function () {
    Route::get('/is-connected', function (Request $request) {
        return response()->json(['isConnected' => true] ,200);
    });
    Route::get('/dashboard', function () {

    });

    Route::get('/user-website', [UserWebsiteController::class, 'index'])
        ->name('websites.store');

    Route::post('/user-website', [UserWebsiteController::class, 'store'])
        ->name('websites.store');

    Route::delete('/user-website/{id}', [UserWebsiteController::class, 'destroy'])
        ->name('websites.destroy');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

//Route::get('/test', function (Request $request) {
//    return response()->json(['message' => 'ok']);
//})->name('test');
