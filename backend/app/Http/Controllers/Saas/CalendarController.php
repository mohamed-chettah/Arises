<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
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

    /**
     * @throws ConnectionException
     */
    public function createEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'date' => 'required',
            'start' => 'required',
            'end' => 'required|',
            'description' => 'nullable|string',
        ]);

        $dateString = sprintf('%04d-%02d-%02d', $validated['date']['year'], $validated['date']['month'], $validated['date']['day']);

        // timezone user

        // 1. Crée l'objet en Europe/Paris directement
        $date = Carbon::createFromFormat('Y-m-d', $dateString, 'Europe/Paris');

        // 2. Combine avec l'heure, toujours en Europe/Paris
        $startDateTime = $date->copy()->setTimeFromTimeString($validated['start']);
        $endDateTime = $date->copy()->setTimeFromTimeString($validated['end']);

        // 3. Convertis en UTC pour l'envoi, mais garde la timeZone dans le payload
        $validated['start'] = $startDateTime->copy()->setTimezone('UTC')->toIso8601String();
        $validated['end'] = $endDateTime->copy()->setTimezone('UTC')->toIso8601String();

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

        [$response, $status] = $this->googleCalendarService->createEvent($payload);

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
