<?php

use App\Http\Controllers\API\LinkController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['auth:api', 'throttle:120'])
    ->group(function () {
        Route::apiResource('links', LinkController::class, [
            'parameters' => ['links' => 'id'],
        ])->middleware('api.guard');

        Route::fallback(fn () => response()->json([
            'message' => 'Resource not found.',
            'status' => 404,
        ], 404));
    });
