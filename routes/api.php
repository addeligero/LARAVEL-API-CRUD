<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class,'logout']);
    Route::post('/upload', [DashboardController::class,'AddImage']);
    Route::put('/passwords', [AuthController::class,'UpdatePass']);
    Route::get('/check-user-uploaded', [DashboardController::class, 'checkUserUploaded']);
    Route::post('uploadMotto', [DashboardController::class, 'uploadMotto']);

});
