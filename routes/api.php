<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix("api")->group(function () {
    Route::get('/videos/{video}', 'VideoController@show');
    Route::post('/videos', [VideoController::class, "store"])->name('videos.store');
});
