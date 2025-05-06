<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleCalendarService
{
    protected string $calendarId = 'primary'; // ou ton calendrier ID exact
    protected string $apiBaseUrl = 'https://www.googleapis.com/calendar/v3';

    public function listEvents(string $start, string $end): array
    {
        $accessToken = env('GOOGLE_CALENDAR_API_KEY', '');

        if (!$accessToken) {
            return ["missing access token", 500];
        }

        $response = Http::withToken($accessToken)
            ->get("$this->apiBaseUrl/calendars/$this->calendarId/events", [
                'timeMin' => $start,
                'timeMax' => $end,
                'singleEvents' => 'true',
                'orderBy' => 'startTime',
            ]);

        return [$response->json(), $response->status()];
    }

    public function createEvent(string $accessToken, array $eventData): array
    {
        $response = Http::withToken($accessToken)
            ->post("{$this->apiBaseUrl}/calendars/{$this->calendarId}/events", $eventData);

        return [$response->json(), $response->status()];
    }
}
