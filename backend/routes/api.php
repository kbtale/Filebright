<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ChatController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/upload', [DocumentController::class, 'upload']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
    Route::patch('/documents/{id}/rename', [DocumentController::class, 'rename']);
    Route::post('/chat', [ChatController::class, 'chat']);
});
