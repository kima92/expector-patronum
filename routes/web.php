<?php
use Illuminate\Support\Facades\Route;
use Kima92\ExpectorPatronum\Http\Controllers\TimelineController;

Route::middleware("web")
    ->prefix(config("expector-patronum.url", "/expector-patronum"))->group(function () {
        Route::get("/", [TimelineController::class, "index"]);
        Route::get("/items", [TimelineController::class, "getItemsBetweenDates"]);
    });
