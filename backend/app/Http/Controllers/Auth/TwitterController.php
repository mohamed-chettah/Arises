<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class TwitterController extends Controller
{
    public function redirectToTwitter(): JsonResponse
    {
        $url = Socialite::driver('twitter')->redirect()->getTargetUrl();
        return response()->json(['url' => $url]);
    }

    public function handleTwitterCallback(): JsonResponse|RedirectResponse
    {
        try {
            $twitterUser = Socialite::driver('twitter')->user();

            // Vérifie si l'utilisateur existe déjà
            $user = User::where('twitter_id', $twitterUser->getId())->first();

            if (!$user) {
                // Si l'utilisateur n'existe pas, crée un nouvel enregistrement
                $user = User::create([
                    'name' => $twitterUser->getName(),
                    'email' => $twitterUser->getEmail() ?: '',
                    'twitter_id' => $twitterUser->getId(),
                    'avatar' => $twitterUser->getAvatar(),
                ]);
            }

            Auth::login($user);

            // Générer un token API (JWT, Sanctum, etc.)
            $token = $user->createToken('auth_token')->plainTextToken;

            // 🔥 Redirection vers l'extension avec le token en paramètre 🔥
            return redirect()->away("chrome-extension://<EXTENSION_ID>/callback.html?token=$token/user=$user");

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
