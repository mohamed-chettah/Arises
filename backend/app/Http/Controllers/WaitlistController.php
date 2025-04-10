<?php

namespace App\Http\Controllers;

use App\Http\Requests\WaitlistRequest;
use App\Services\WaitlistService;
use Illuminate\Http\JsonResponse;

class WaitlistController
{
    public function store(WaitlistRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
             WaitlistService::create($validated);

            return response()->json("Ajout à la liste d'attente réussi", 201);
        }
        catch (\Throwable $e) {
            \Sentry\captureException($e); // ← Ajout important ici
            return response()->json("Erreur lors de l'ajout à la liste d'attente", 500);
        }
    }

    public function mailVerification(WaitlistRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            WaitlistService::mailVerification($validated);

            return response()->json("Mail de vérification envoyé", 201);
        }
        catch (\Throwable $e) {
            \Sentry\captureException($e);
            return response()->json("Erreur lors de l'envoi du mail de vérification", 500);
        }
    }
}
