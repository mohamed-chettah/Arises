<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIChatService
{
    protected string $apiUrl = 'https://api.openai.com/v1/chat/completions';
    protected string $model = 'gpt-4o';

    public function ask(string $userPrompt, array $calendar = [], string $objective = null): ?string
    {
        $messages = [];

        // 1. Système de rôle
        $messages[] = [
            'role' => 'system',
            'content' => 'You are a productivity assistant. You help users reach their learning or personal goals by suggesting tasks and scheduling sessions based on their weekly calendar.'
        ];

        // 2. Ajout du prompt utilisateur
        $fullPrompt = $userPrompt;

        // 3. Si un calendrier est fourni
        if (!empty($calendar)) {
            $calendarJson = json_encode($calendar, JSON_PRETTY_PRINT);
            $fullPrompt .= "\n\nHere is my calendar:\n\n" . $calendarJson;
            $fullPrompt .= "\n\nCan you help me organize a learning plan based on my availability?";
        }

        $messages[] = [
            'role' => 'user',
            'content' => $fullPrompt
        ];

        // 4. Requête HTTP vers OpenAI
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post($this->apiUrl, [
                'model' => $this->model,
                'temperature' => 0.7,
                'messages' => $messages
            ]);

        // 5. Gérer les erreurs
        if ($response->failed()) {
            logger()->error('OpenAI API failed', ['response' => $response->body()]);
            return null;
        }

        // 6. Retourner le texte généré
        return $response->json('choices.0.message.content');
    }
}
