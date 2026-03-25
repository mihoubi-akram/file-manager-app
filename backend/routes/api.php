<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\File\FileController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/files', [FileController::class, 'index']);
    Route::post('/files', [FileController::class, 'store'])->middleware('throttle:20,1');
    Route::get('/files/{userFile}/download', [FileController::class, 'download']);
    Route::delete('/files/{userFile}', [FileController::class, 'destroy']);
});
