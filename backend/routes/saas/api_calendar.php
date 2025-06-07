<?php

use App\Http\Controllers\Saas\CalendarController;
use Illuminate\Support\Facades\Route;

Route::prefix('calendar')->middleware(['jwt','throttle:20,1'])->group(function () {
    Route::get('/events', [CalendarController::class, 'listEvents']);
    Route::post('/event', [CalendarController::class, 'createEvent']);
    Route::put('/event/{eventId}', [CalendarController::class, 'updateEvent']);
});
