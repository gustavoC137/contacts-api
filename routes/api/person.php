<?php

use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return 'Hello API';
});

Route::apiResource('person', PersonController::class);
