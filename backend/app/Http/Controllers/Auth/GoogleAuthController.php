<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
        $url = Socialite::driver('google')->stateless()->redirect(env('APP_URL') . '/api/app/auth/google/callback')->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    public function handleGoogleCallback(): RedirectResponse | JsonResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            if (!$googleUser) {
                throw new \Exception("Google dont return user data.");
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

            $token = JWTAuth::fromUser($user);

            // return to the frontend
            return redirect()->away(env('FRONTEND_URL') . "/dashboard")
                ->cookie('token', $token, 60, '/', 'localhost', false, env('APP_ENV') === 'production')
                ->cookie('user', json_encode($user), 60, '/', 'localhost', false, env('APP_ENV') === 'production');

            // TODO ENVOYER UN Refresh Token aussi

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
