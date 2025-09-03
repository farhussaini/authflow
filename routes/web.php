<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OAuthController;

// OAuth PKCE routes
Route::get('/auth/redirect', [OAuthController::class, 'redirect'])->name('oauth.redirect');
Route::get('/auth/callback', [OAuthController::class, 'callback'])->name('oauth.callback');
Route::post('/auth/logout', [OAuthController::class, 'logout'])->middleware('auth')->name('oauth.logout');

// Include API routes manually
require base_path('routes/api.php');

// Catch-all route for Vue SPA
Route::get('/{any}', function () {
    return view('app'); // app.blade.php
})->where('any', '.*');