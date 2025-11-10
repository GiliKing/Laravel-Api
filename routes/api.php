<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {

    // Public routes
    Route::post('/register', [AuthenticationController::class, 'register']);
    Route::post('/login', [AuthenticationController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // logout
        Route::post('/logout', [AuthenticationController::class, 'logout']);

        // users
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'create'])->middleware('admin');
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update'])->middleware('admin');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('admin');



        Route::get('/total-salary', [UserController::class, 'totalSalary'])->middleware('admin');
    });
});