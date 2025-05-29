<?php

use App\Http\Controllers\Saas\ArisesAiController;
use Illuminate\Support\Facades\Route;

Route::prefix('arises-ai')->middleware(['jwt','throttle:20,1'])->group(function () {
    Route::post('/ask', [ArisesAiController::class, 'ask']);
});
