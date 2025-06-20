<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GoogleCalendar;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle(): JsonResponse
    {
        $url = Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/calendar',
            ])
            ->with([
                'prompt' => 'consent',
                'access_type' => 'offline',
            ])
            ->stateless()->redirect(env('APP_URL') . '/api/app/auth/google/callback')->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    public function handleGoogleCallback(): RedirectResponse | JsonResponse
    {
        try {
            if (request()->has('error')) {
                return redirect()->away(env('FRONTEND_URL') . "/login?error=access_denied")
                    ->with('error', 'Vous avez annulé la connexion avec Google.');
            }

            $googleUser = Socialite::driver('google')->stateless()->user();

            if (!$googleUser) {
                return redirect()->away(env('FRONTEND_URL') . "/login?error=google_auth_failed")
                    ->with('error', 'Google authentication failed. Please try again.');
            }

            $user = User::where('google_id', $googleUser->getId())->OrWhere('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail() ?: '',
                    'email_verified_at' => now(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            // replace if exist the calendar credentials
            GoogleCalendar::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'access_token' => $googleUser->token,
                    'refresh_token' => $googleUser->refreshToken,
                    'expires_in' => now()->addSeconds($googleUser->expiresIn),
                ]
            );

            $token = JWTAuth::fromUser($user);

            // Génération du refresh token avec une durée de vie plus longue
            $refreshToken = JWTAuth::customClaims([
                'type' => 'refresh',
                'exp' => now()->addMinutes(config('jwt.refresh_ttl'))->timestamp
            ])->fromUser($user);

            // return to the frontend
            return redirect()->away(env('FRONTEND_URL') . "/dashboard")
                ->cookie('token', $token, 60, '/', env('URL'), false, env('APP_ENV') === 'production')
                ->cookie('refresh_token', $refreshToken, config('jwt.refresh_ttl'), '/', env('URL'), false, env('APP_ENV') === 'production')
                ->cookie('user', json_encode($user), 60, '/', env('URL'), false, env('APP_ENV') === 'production');

        } catch (\Exception $e) {
            // Log the error to Sentry
            \Sentry\captureException($e);
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

}
