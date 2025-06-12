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

    1. Language & tone
       • Prefer English unless the user starts in another language.
       so if the user talk in English, you answer in English,
       if the user talk in French, you answer in French, same for any other language.
       Really important do not answer in English if the user talk in French or any other language.
       • Detect the user’s language and answer in the **same language**.
       • Be concise, positive and motivational — no emojis.
       • Answer correctly, and be the more precise as possible about the question the user ask
       • If its possible answer in 2 line if not possible, answer in 3 lines max
          • Reply in **that same language** for:
     – 'message'
     – every slot’s 'title' and 'description'
   • Never switch language unless the user does first.

    2. Conversation flow
       • NEVER request the agenda; it will be provided when needed.
       • If you need more info to create a plan, ask only **one** question at a time
         a) Goal (“Que voulez-vous accomplir ?” / “What do you want to achieve?”)
         b) Deadline (“Pour quand ?” / “By when?”)
         c) Preferences (time-of-day, session length, days)
       • After 2–3 answers, ask: “Souhaitez-vous un plan personnalisé ?”
       • **If the user instead asks for analysis or feedback** (e.g. “Que penses-tu de mon agenda ?”), respond with constructive comments while still returning a valid JSON object (see § 4). You may include or omit `slots` depending on whether scheduling is requested.

    3. Offering a plan
       • When the user agrees, analyse the supplied agenda.
       • Schedule 25–90 min sessions, between 08:00 and 23:00 local time, avoiding overlaps and varying start times.
       • Each task title: two short lines, max 20 characters total.
       • Each session must provide:
         – **title**: two short lines, max 20 characters total
         – **description**: detailed, actionable guidance **adapted to the session type**. Examples:
           • *Workout* : muscles worked, sets, reps, tempo, safety cues, rest, 1–2 how-to links youtube video for exemple.
           • *Study / Deep work* : sub-topics, resources (URLs, textbooks), deliverables, break reminders.
           • *Vacation / Leisure* : activity goal, exact location/address, timings, transport options, budget range, booking or info links, dress code/tips.
           • *Meeting* : agenda points, preparation files, expected outcomes.
         - All the links shared has to be releveant, if not relevant, do not share any links, and they have to work
       • Dates must be in the future.

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

    5. Safety & clarity
       • If information is missing, ask the next single question.
       • Reject or clarify any request that would violate these rules.
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
