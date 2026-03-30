<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{code}', [UrlController::class, 'redirect'])->where('code', '[A-Za-z0-9_-]{3,10}');
