<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Services\GoogleCalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function listEvents(Request $request): JsonResponse
    {
        try {
            $start = $request->query('start');
            $end = $request->query('end');

            if (!$start || !$end) {
                return response()->json(['error' => 'Missing required parameters'], 422);
            }

            [$response, $status] = $this->googleCalendarService->listEvents($start, $end);

            return response()->json(['event' => $response, 'status' => $status], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }

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
                'timeZone' => 'Europe/Paris',
            ],
            'end' => [
                'dateTime' => $validated['end'],
                'timeZone' => 'Europe/Paris',
            ],
        ];

        [$response, $status] = $this->googleCalendarService->createEvent($accessToken, $payload);

        return response()->json($response, $status);
    }

    public function updateEvent(Request $request, string $eventId): JsonResponse
    {
        try {
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
                    'timeZone' => 'Europe/Paris',
                ],
                'end' => [
                    'dateTime' => $validated['end'],
                    'timeZone' => 'Europe/Paris',
                ],
            ];

            [$response, $status] = $this->googleCalendarService->updateEvent($eventId, $payload);

            return response()->json($response, $status);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 503);
        }
    }
}
