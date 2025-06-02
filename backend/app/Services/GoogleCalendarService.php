<?php

namespace App\Services;

use App\Models\GoogleCalendar;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GoogleCalendarService
{
    protected string $calendarId = 'primary'; // ou ton calendrier ID exact
    protected string $apiBaseUrl = 'https://www.googleapis.com/calendar/v3';

    /**
     * @throws ConnectionException
     * @throws \Exception
     */
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

        if($response->status() == 401){
            return [];
        }

        return [$response->json(), $response->status()];
    }

    public function createEvent(string $accessToken, array $eventData): array
    {
        $response = Http::withToken($accessToken)
            ->post("{$this->apiBaseUrl}/calendars/{$this->calendarId}/events", $eventData);

        return [$response->json(), $response->status()];
    }

    public function updateEvent(string $accessToken, array $eventData): array
    {
        $response = Http::withToken($accessToken)
            ->post("{$this->apiBaseUrl}/calendars/{$this->calendarId}/events", $eventData);

        return [$response->json(), $response->status()];
    }

    /**
     * @throws \Exception
     */
    public function getCredentials(): string
    {
        $user = auth()->user();
        $googleCalendar = $this->findByGoogleId($user->google_id);

        if (Carbon::now() >= $googleCalendar->expires_in) {
            $googleCalendar = $this->refreshGoogleAccessToken($googleCalendar);
        }

        return $googleCalendar->access_token;
    }

    public function findByGoogleId(string $googleId): ?GoogleCalendar
    {
        return GoogleCalendar::where('google_id', $googleId)->first();
    }

    function refreshGoogleAccessToken($googleCalendar): ?GoogleCalendar
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'refresh_token' => $googleCalendar->refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->failed()) {
            throw new \Exception('Impossible de rafraîchir le token Google.');
        }

        $data = $response->json();

        // Mise à jour en BDD
        $googleCalendar->update([
            'access_token' => $data['access_token'],
            'expires_in' => Carbon::now()->addSeconds($data['expires_in']),
        ]);

        return $googleCalendar;
    }
}
