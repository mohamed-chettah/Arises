<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class OpenAIChatService
{
    protected string $apiUrl = 'https://api.openai.com/v1/chat/completions';
    protected string $model = 'gpt-4o';

    public function ask(string $userPrompt, Collection $history , array $calendar = []): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => "You are a productivity assistant. Your job is to help the user reach goals by asking questions and proposing planning slots based on their agenda.

        ⚠️ Always follow these strict rules:

        1. Ask ONE question at a time.
           Question order:
           - Current level?
           - Specific goal?
           - Preferences (morning/evening, duration)?

        2. NEVER ask for the agenda — it will always be provided in the conversation.

        3. After 2-3 answers, ask: 'Do you want a personalized plan?'

        4. If the user agrees, analyze the agenda and return a planning with different task (2 line per task for the title) and use different hours **in this EXACT format**:
        {
          \"message\": \"Your readable answer with motivation and emojis\",
          \"slots\": [
            {
              \"title\": \"Session name\",
              \"start\": \"2025-05-06T10:00:00\",
              \"end\": \"2025-05-06T11:00:00\"
            }
          ]
        }

        5. Be motivating and positive.

        ⚠️ Always return:
        {
          \"message\": \"...\",
          \"slots\": [...]
        }

        - `message`: must ALWAYS be present.
        - `slots`: either proposed sessions or an empty array.
        - NO Markdown, NO triple quotes, NO extra text, just RAW JSON.

        🛑 Your response MUST ALWAYS be valid JSON with `message` and `slots` fields. Reply only in English."
            ]
        ];

        // 2. Ajouter historique s'il existe
        foreach ($history as $message) {
            $messages[] = [
                'role' => $message->role,
                'content' => $message->content
            ];
        }

        $formattedCalendar = collect($calendar['items'] ?? [])
            ->map(function ($event) {
                return [
                    'title' => $event['summary'] ?? '',
                    'start' => $event['start']['dateTime'] ?? null,
                    'end' => $event['end']['dateTime'] ?? null,
                ];
            })
            ->filter(fn ($e) => $e['start'] && $e['end']) // Ne garder que les valides
            ->values()
            ->all();


        $messages[] = [
            'role' => 'system',
            'content' => "Voici l'emploi du temps de l'utilisateur :\n\n" . json_encode($formattedCalendar)
        ];

        $messages[] = [
            'role' => 'user',
            'content' => $userPrompt
        ];

        // 4. Appel OpenAI
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post($this->apiUrl, [
                'model' => $this->model,
                'temperature'    => 0.2,
                'response_format'=> ['type' => 'json_object'],
                'messages' => $messages
            ]);

        if ($response->failed()) {
            logger()->error('OpenAI API failed', ['response' => $response->body()]);
            return [];
        }

        // 5. Vérification de la réponse
        if (!isset($response['choices'][0]['message']['content'])) {
            logger()->error('Invalid response from OpenAI', ['response' => $response->body()]);
            return [];
        }
        $responseText = $response->json('choices.0.message.content');

        // Supprimer les éventuels """ ou ```json ou autres entourages
        // Nettoyage fiable du JSON brut même s'il est encadré par des """ ou texte inutile
        $cleaned = trim($responseText);
        $match = [];

        if (preg_match('/\{.*\}/s', $cleaned, $match)) {
            $cleaned = $match[0]; // On récupère uniquement le JSON brut
        } else {
            logger()->error('Unable to extract JSON from GPT response', ['raw' => $responseText]);
            return [];
        }

        // Tenter le décodage
        $data = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            logger()->error('Invalid JSON from OpenAI', ['raw' => $responseText]);
            return [];
        }

        return $data;
    }
}
