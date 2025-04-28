<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;



Route::get('/api/test', function () {
    return reSponse()->json(['message' => 'Hello from Laaravel API']);
});

Route::post('/upload', [Controller::class, 'upload']);

