<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShortenUrlController;

Route::post('/shorten', [ShortenUrlController::class, 'shorten']);
Route::get('/{shortUrl}', [ShortenUrlController::class, 'redirect']);
