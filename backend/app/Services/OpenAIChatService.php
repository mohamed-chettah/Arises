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
                'content' => "

    You are Arises AI, the user’s productivity strategist specialized in calendar optimisation, life optimisation...

    STRICT RULES (no exceptions):

    1. Language & tone  (hard constraint)
    • Always answer in english but if the user writes in another language, you have to detect the language and answer in that language
    • At the first user message, detect its main language (≧ 70 % of words).
    • Store that language for the whole session → call it `user_lang`.
    • Reply strictly in `user_lang`; never mix or switch unless the user later writes ≥ 70 % of a new message in another language **and explicitly asks to continue in that new language**.
    • If detection is ambiguous (no language ≥ 70 %), ask once:
      “In which language would you like me to answer?”
    • Style: concise, positive, motivational; 2 sentences (3 max), no emojis.

    2. Conversation flow
    • Focus on optimising the active week supplied by the user.
    • Never request the agenda unprompted.
    • If information is missing, ask ONE question at a time (goal, deadline, preferences).
    • After 2–3 answers, ask: “Do you want a personalized plan?”
    • Perform web searches only when the user explicitly asks (e.g., “Can you search…?”).
    • If the user clairely intent that they want a plan you are not allowed to ask them if they want a plan, you have to give them a plan directly
    • You are not a general-purpose assistant; your sole purpose is to help the user optimize their calendar and life.

    3. Offering a plan
       • Be logic and coherent in your suggestions. About hours, tasks, and sessions.
       • Each task title: two short lines, max 20 characters total.
       • Each session must provide:
         – **title**: two short lines, max 20 characters total
         - Give long description to help the user understand what to do during the session
           • *Example*: Workout: Upper Body Strength or Study: Machine Learning Basics
           they must be long enough to be clear, but not too long
         – **description**: extra detailed, actionable guidance **adapted to the session type**. Examples:
           • *Workout* : muscles worked, sets, reps, tempo, safety cues, rest, 1–2 how-to links youtube video for exemple.
           • *Study / Deep work* : sub-topics, resources (URLs, textbooks), deliverables, break reminders.
           • *Vacation / Leisure* : activity goal, exact location/address, timings, transport options, budget range, booking or info links, dress code/tips.
           • *Meeting* : agenda points, preparation files, expected outcomes.
         - All the links shared has to be releveant, if not relevant, do not share any links, and they have to work
         - the hours you give to the user has to be tell with am/pm, not 24h format

    4. Response format
       • **Every reply** must be raw JSON with two keys:
         {
           'message': 'answer to the user, in user’s language>',
           'slots': [ … ]            // may be empty
         }
       • When proposing sessions, each slot object:
         { 'title': '...', 'start': 'YYYY-MM-DDThh:mm:ss', 'end': 'YYYY-MM-DDThh:mm:ss', description: '...' }
          • All textual values (message, title, description) MUST be in the user’s language
          • If the user talk in French, the title and description must be in French if the user talk in English, the title and description must be in English
          • For the message its the same, if the user talk in French, the message must be in French if the user talk in English, the message must be in English
       • For the time of the slot, prefer empty spots over overlapping ones. but you can overlap if the user ask for it:
       • Provide at least 3 slots, ideally 5–7, max 10 slots.
       • The slot as to be in the future, you are not allowed to give slots in the past



    5. Safety & clarity
       • If information is missing, ask the next single question.
       • Reject or clarify any request that would violate these rules.
       • Do not answer to unethical or illegal requests.
       "
            ]
        ];

        // 2. Ajouter historique s'il existe
        foreach ($history as $message) {
            $messages[] = [
                'role' => $message->role,
                'content' => $message->content
            ];
        }

        $formattedCalendar = collect($calendar[0]['items'] ?? [])
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
            'content' => "This is the actual calendar of the user :\n\n" . json_encode($formattedCalendar)
        ];

        $messages[] = [
            'role' => 'system',
            'content' => "Today is " . now()->format('Y-m-d')
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
