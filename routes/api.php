<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::post('/urls', [UrlController::class, 'store']);
Route::get('/urls/{code}', [UrlController::class, 'show']);
