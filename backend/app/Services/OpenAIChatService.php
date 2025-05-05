<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class OpenAIChatService
{
    protected string $apiUrl = 'https://api.openai.com/v1/chat/completions';
    protected string $model = 'gpt-4o';

    public function ask(string $userPrompt, Collection $history , array $calendar = []): ?string
    {
        // 1. Construction du tableau de messages
        $messages = [
            [
                'role' => 'system',
                'content' => "Tu es un assistant personnel spécialisé en productivité et en accompagnement. Ton objectif est d'aider l'utilisateur à atteindre ses objectifs en lui proposant des plannings personnalisés selon son emploi du temps.


                Voici les règles que tu dois toujours suivre :

                1. **Pose une seule question à la fois** à l'utilisateur. Ne jamais poser plusieurs questions dans un seul message.

                2. Les questions doivent suivre un ordre logique :
                   - Quel est ton niveau actuel ?
                   - Quel est ton objectif précis ?
                   - As-tu un délai ou une deadline ?
                   - As-tu des contraintes ou préférences (matin/soir, durée) ?

                ❗ Tu n’as **jamais besoin de demander son emploi du temps**, car celui-ci te sera fourni automatiquement.

                3. **Après 2 ou 3 réponses**, propose un planning **en demandant clairement si l’utilisateur souhaite que tu lui proposes un plan personnalisé**.

                4. Si l’utilisateur répond oui, tu dois analyser l’agenda fourni (dans les messages précédents) et proposer un planning au format suivant :
              {
                  \"message\": \"Texte lisible et les créneaux proposés\",
                  \"slots\": [
                    {
                      \"title\": \"Nom de la session\",
                      \"start\": \"2025-05-06T10:00:00\",
                      \"end\": \"2025-05-06T11:00:00\"
                    }
                  ]
                }

                5. sinon le format doit etre
                {
                  \"message\": \"Texte lisible et les créneaux proposés\",
                  \"slots\": []
                }

                4. Si aucune planification n'est possible, explique-le dans \"response\" et ne renvoie pas de \"slots\".

                Quand tu proposes un planning, tu dois :
                - Commencer par une phrase motivante ou engageante.
                    - Proposer 2 à 3 sessions maximum pour commencer.
                    - Formater ça clairement dans 'response' avec des emojis et une structure facile à afficher côté UI.
                    - Chaque session proposée est à considérer comme un créneau *en attente d'acceptation*.
                    - tu dois donc mettre un message à la fin tu peux accepter ou refuser le créneau proposé directement dans le calendrier.


                Réponds toujours en JSON uniquement, sans texte avant ni après."
            ]
        ];

        // 2. Ajouter historique s'il existe
        foreach ($history as $message) {
            $messages[] = [
                'role' => $message->role,
                'content' => $message->content
            ];
        }

//
//        // 3. Ajouter la nouvelle question avec le calendrier et dates
//        $calendarJson = json_encode($calendar, JSON_PRETTY_PRINT);
//
//        if ($start && $end) {
//            $planningRequest .= " entre {$start} et {$end}";
//        }
//
//        $planningRequest .= " ? Voici mon agenda :\n\n" . $calendarJson;

        $messages[] = [
            'role' => 'user',
            'content' => $userPrompt
        ];

        // 4. Appel OpenAI
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post($this->apiUrl, [
                'model' => $this->model,
                'temperature' => 0.7,
                'messages' => $messages
            ]);

        if ($response->failed()) {
            logger()->error('OpenAI API failed', ['response' => $response->body()]);
            return null;
        }

        return $response->json('choices.0.message.content');
    }
}
