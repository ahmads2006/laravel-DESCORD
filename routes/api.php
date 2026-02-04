<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SpecializationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working properly ✅'
    ]);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Logout endpoint
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    
    // Language Selection
    Route::get('/languages', [LanguageController::class, 'index']);
    Route::post('/languages', [LanguageController::class, 'store']);

    // Specialization Selection
    Route::get('/specializations', [SpecializationController::class, 'index']);
    Route::post('/specializations', [SpecializationController::class, 'store']);

    // Quiz
    // 'start' checks if user can take quiz and returns questions
    Route::get('/quiz/{specialization}', [QuizController::class, 'start']); 
    Route::post('/quiz/{test}/submit', [QuizController::class, 'submit']);
    Route::post('/quiz/{test}/autosave', [QuizController::class, 'autosave']);

    // Results
    Route::get('/results/{test}', [ResultController::class, 'show']);
    Route::get('/history', [ResultController::class, 'history']);
});
