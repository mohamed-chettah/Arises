<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Services\OpenAIChatService;
use App\Services\UserProgressionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArisesAiController extends Controller
{
    protected OpenAIChatService $openAIChatService;
    public function __construct(OpenAIChatService $openAIChatService)
    {
        $this->openAIChatService = $openAIChatService;
    }

    public function ask(Request $request, )
    {
        $calendar = [
            // Récupère ton calendrier depuis DB ou autre
        ];

        $response = $this->openAIChatService->ask("I want to learn English", $calendar);

        return response()->json([
            'ai_response' => $response
        ]);
    }

}
