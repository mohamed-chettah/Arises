<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    protected GoogleCalendarService $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }
}
