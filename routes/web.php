<?php
use Illuminate\Support\Facades\Route;
use Kima92\ExpectorPatronum\Http\Controllers\ExpectationPlansController;
use Kima92\ExpectorPatronum\Http\Controllers\GroupsController;
use Kima92\ExpectorPatronum\Http\Controllers\TimelineController;

Route::middleware("web")
    ->prefix(config("expector-patronum.url"))->group(function () {
        Route::get("/", [TimelineController::class, "index"]);
        Route::get("/items", [TimelineController::class, "getItemsBetweenDates"]);
        Route::prefix("/expectation-plans")->group(function () {
            Route::get("/", [ExpectationPlansController::class, "index"]);
            Route::post("/", [ExpectationPlansController::class, "store"]);
        });
        Route::prefix("/groups")->group(function () {
            Route::get("/", [GroupsController::class, "index"]);
            Route::post("/", [GroupsController::class, "store"]);
        });
    });
