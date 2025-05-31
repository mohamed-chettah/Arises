<?php

namespace App\Services;

use App\Models\GoogleCalendar;
use Illuminate\Support\Facades\Http;

class GoogleCalendarService
{
    protected string $calendarId = 'primary'; // ou ton calendrier ID exact
    protected string $apiBaseUrl = 'https://www.googleapis.com/calendar/v3';

    public function listEvents(string $start, string $end): array
    {
        $accessToken = $this->getCredentials();

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

    public function getCredentials(): string
    {
        $user = auth()->user();
        $googleCalendar = $this->findByGoogleId($user->google_id);

        return $googleCalendar->access_token;
    }

    public function findByGoogleId(string $googleId): ?GoogleCalendar
    {
        return GoogleCalendar::where('google_id', $googleId)->first();
    }
}
