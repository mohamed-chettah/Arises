<?php


namespace App\Http\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class JwtAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(["error" => "Unauthorized"], 401);
        }

        // Retirer le préfixe "Bearer " du token s'il existe
        $token = str_replace('Bearer ', '', $token);

        try {
            // Décoder le token
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            // Vérifier si l'utilisateur existe en BDD
            $user = User::find($decoded->sub);

            if (!$user) {
                return response()->json(["error" => "Invalid Token"], 401);
            }

            $request->user = $user; // Ajoute l'user à la requête
        } catch (\Exception $e) {
            return response()->json(["error" => "Invalid Token"], 401);
        }

        return $next($request);
    }
}
