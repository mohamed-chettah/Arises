<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOrigin
{
    public function handle(Request $request, Closure $next)
    {
        $allowedOrigin = 'https://arises.app';

        $origin = $request->headers->get('origin') ?? $request->headers->get('referer');

        if ($origin && !str_starts_with($origin, $allowedOrigin)) {
            return response()->json(['message' => 'Origine non autorisée.'], 403);
        }

        return $next($request);
    }
}
