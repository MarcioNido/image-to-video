<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/videos/{video}', [VideoController::class, "show"]);
    Route::post('/videos', [VideoController::class, "store"])->name('videos.store');
});
