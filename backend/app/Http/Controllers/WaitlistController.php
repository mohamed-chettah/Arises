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
        dd($validated);
        try {
             WaitlistService:: create($validated);

            return response()->json("Ajout à la liste d'attente réussi", 201);
        }
        catch (\Throwable $e) {
            return response()->json("Erreur lors de l'ajout à la liste d'attente", 500);
        }
    }
}
