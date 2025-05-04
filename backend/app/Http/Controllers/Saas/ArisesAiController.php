<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Saas\OpenAiRequest;
use App\Services\ChatService;
use App\Services\OpenAIChatService;

class ArisesAiController extends Controller
{
    protected OpenAIChatService $openAIChatService;
    protected ChatService $chatService;
    public function __construct(OpenAIChatService $openAIChatService, ChatService $chatService)
    {
        $this->chatService = $chatService;
        $this->openAIChatService = $openAIChatService;
    }

    public function ask(OpenAiRequest $request)
    {
        $validated = $request->validated();

        // en param on recupere la start et end du user
        // et on va chercher le calendrier de l'utilisateur pour le passer en params
        //

        // on recupere aussi la question de l'utilisateur

        // TODO on stocke en bdd le message du user
        $this->chatService->create([
            'user_id' => auth()->id(),
            'role' => 'user',
            'content' => $validated["question"]
        ]);

        $calendar = [
            // Récupère ton calendrier depuis DB ou autre (google calendar)
        ];

        $response = $this->openAIChatService->ask($validated["question"], $calendar);
        $this->chatService->create([
            'user_id' => auth()->id(),
            'role' => 'assistant',
            'content' => $response
        ]);

        // TODO on stocke en bdd la réponse du chat



        return response()->json([
            'ai_response' => $response
        ]);
    }

}
