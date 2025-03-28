<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserWebsiteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleController extends Controller
{
    public function redirectToGoogle(): JsonResponse
    {
        // Obtenir l'URL de redirection générée par Socialite
        $url = Socialite::driver('google')->redirect()->getTargetUrl();

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

                $datas = [
                    'user_id' => $user->id,
                    'website_id' => 3
                ];
                UserWebsiteService::create($datas);
            }

            $token = JWTAuth::fromUser($user);

            $refreshToken = auth()->claims(['refresh' => true])->setTTL(10080)->fromUser($user);

            $authKey = Str::random(40);

            Cache::put("auth:$authKey", [
                'token' => $token,
                'refresh_token' => $refreshToken,
                'user' => $user
            ], now()->addMinutes(20));

            // return to the extension
            return redirect()->away("chrome-extension:/fjjenhlhpcdimhbdbfachdhndiiejgjo/views/oauth.html?token=$authKey");

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

}
