<?php

use App\Http\Controllers\Extension\SessionFocusController;
use Illuminate\Support\Facades\Route;

Route::prefix('focus')->middleware(['jwt', 'throttle:20,1'])->group(function () {
    Route::post('/start', [SessionFocusController::class, 'start']);
    Route::post('/pause', [SessionFocusController::class, 'pause']);
    Route::post('/resume', [SessionFocusController::class, 'resume']);
    Route::post('/finish', [SessionFocusController::class, 'finish']);
});
