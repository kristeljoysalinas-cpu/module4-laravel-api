<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureApiKey;
use Illuminate\Support\Facades\Route;

// Rate limit: at most 60 requests per minute per client
Route::middleware('throttle:60,1')->group(function () {
    // Public: list users
    Route::get('/users', [UserController::class, 'index']);

    // Protected: creating a user requires a valid X-API-KEY header
    Route::post('/users', [UserController::class, 'store'])->middleware(EnsureApiKey::class);
});
