<?php

use App\Http\Controllers\GemController;
use Illuminate\Support\Facades\Route;


Route::get('/', [GemController::class, 'index']);
Route::get('/gems-data', [GemController::class, 'fetchData']);
