<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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


    public function handleGoogleCallback()
    {
        try {

            $googleUser = Socialite::driver('google')->stateless()->user();

            if (!request()->has('code')) {
                return response()->json([
                    'error' => 'Code OAuth manquant. Google n\'a pas redirigé avec un code.',
                    'query' => request()->all() // Voir ce qui est renvoyé
                ], 400);
            }

            // Vérifie si Google a bien renvoyé des données
            if (!$googleUser) {
                throw new \Exception("Google n'a pas renvoyé d'utilisateur.");
            }

            // Vérifie si l'utilisateur existe déjà
            $user = User::where('google_id', $googleUser->getId())->first();

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

            // 🔥 Générer un `authKey` unique et stocker les données temporairement
            $authKey = Str::random(40);
            Cache::put("auth:$authKey", ['token' => $token, 'user' => $user], now()->addMinutes(5));

            // return to arises.app with the token
            return redirect()->away("https://arises.vercel.app/connexion?token=$authKey");
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }


}
