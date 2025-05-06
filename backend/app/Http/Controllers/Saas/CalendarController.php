<?php

namespace App\Http\Controllers\Saas;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CalendarController
{
    protected string $calendarId = 'primary'; // ou ton calendrier ID exact
    protected string $apiBaseUrl = 'https://www.googleapis.com/calendar/v3';

    public function listEvents(Request $request)
    {
        $accessToken = env('GOOGLE_CALENDAR_API_KEY', '');

        if (!$accessToken) {
            return response()->json(['error' => 'Missing access token'], 422);
        }

        $start = $request->query('start');
        $end = $request->query('end');

        if (!$start || !$end || !$accessToken) {
            return response()->json(['error' => 'Missing required parameters'], 422);
        }


        return response()->json($response->json(), $response->status());
    }

    public function createEvent(Request $request)
    {
        $accessToken = env('GOOGLE_CALENDAR_API_KEY', '');

        if (!$accessToken) {
            return response()->json(['error' => 'Missing access token'], 422);
        }

        $validated = $request->validate([
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'description' => 'nullable|string',
        ]);

        $payload = [
            'summary' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'start' => [
                'dateTime' => $validated['start'],
                'timeZone' => 'Europe/Paris', // adapte à ton besoin
            ],
            'end' => [
                'dateTime' => $validated['end'],
                'timeZone' => 'Europe/Paris',
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post("{$this->apiBaseUrl}/calendars/{$this->calendarId}/events", $payload);

        return response()->json($response->json(), $response->status());
    }
}
