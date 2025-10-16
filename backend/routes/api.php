<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/modules', [ModuleController::class, 'index']);
    Route::post('/modules/{id}/activate', [ModuleController::class, 'activate']);
    Route::post('/modules/{id}/deactivate', [ModuleController::class, 'deactivate']);

    Route::middleware('check.module.active')->group(function () {
        Route::post('/shorten', [ShortLinkController::class, 'shorten']);
        Route::get('/links', [ShortLinkController::class, 'index']);
        Route::delete('/links/{id}', [ShortLinkController::class, 'destroy']);
    });
});

Route::get('/s/{code}', [ShortLinkController::class, 'redirect']);