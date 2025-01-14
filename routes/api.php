<?php

use Illuminate\Support\Facades\Route;

/*
 |--------------------------------------------------------------------------
 | API Routes
 |--------------------------------------------------------------------------
 */

Route::middleware('api')->group(function () {
    Route::post('/summarize', [App\Http\Controllers\SummarizeController::class, 'summarize']);
});
