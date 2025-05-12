<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Saas\OpenAiRequest;
use App\Models\Chat;
use App\Services\ChatService;
use App\Services\GoogleCalendarService;
use App\Services\OpenAIChatService;

class ArisesAiController extends Controller
{
    protected OpenAIChatService $openAIChatService;
    protected ChatService $chatService;
    public function __construct(OpenAIChatService $openAIChatService,
                                ChatService $chatService,
                                GoogleCalendarService $googleCalendarService)
    {
        parent::__construct($googleCalendarService);
        $this->chatService = $chatService;
        $this->openAIChatService = $openAIChatService;
    }

    public function ask(OpenAiRequest $request)
    {
        $validated = $request->validated();

        // en param on recupere la start et end du user
        // et on va chercher le calendrier de l'utilisateur pour le passer en params
        //

        if(!auth()->user()){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $this->chatService->create([
            'user_id' => auth()->id(),
            'role' => 'user',
            'content' => $validated["question"]
        ]);


        $calendar = $this->googleCalendarService->listEvents($validated['start'], $validated['end']);

        $history = $this->chatService->getLastMessage();

        $response = $this->openAIChatService->ask($validated["question"], $history, $calendar);

        if(!$response || empty($response['message'])) {
            return response()->json([
                'ai_response' => 'Error while processing your request. Please try again later.',
            ], 500);
        }

        $this->chatService->create([
            'user_id' => auth()->id(),
            'role' => 'assistant',
            'content' => $response['message']
        ]);
        return response()->json([
            'ai_response' => $response
        ]);
    }



}
