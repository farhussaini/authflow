<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;

// API routes for unified OAuth profile
Route::middleware('auth')->get('/me', [ProfileController::class, 'me'])->name('api.me');