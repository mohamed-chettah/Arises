<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;

class GoogleCalendarService
{
    protected string $apiBaseUrl = 'https://www.googleapis.com/calendar/v3';
    protected string $calendarId = 'primary'; // ou ton calendrier ID exact

    public function listEvents(string $accessToken, string $start, string $end): array
    {
        if (!$accessToken) {
            throw new \Exception('Missing access token');
        }

        if (!$start || !$end) {
            throw new \Exception('Missing required parameters');
        }

        $response = Http::withToken($accessToken)
            ->get("{$this->apiBaseUrl}/calendars/{$this->calendarId}/events", [
                'timeMin' => $start,
                'timeMax' => $end,
                'singleEvents' => true,
                'orderBy' => 'startTime',
            ]);

        return $response->json();
    }
}
