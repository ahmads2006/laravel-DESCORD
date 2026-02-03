<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SpecializationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $membersCount = \App\Models\User::count();
    $badgesCount = \App\Models\Test::where('passed', true)->count();
    
    return view('welcome', compact('membersCount', 'badgesCount'));
});

// Auth
Route::get('/auth/discord', [AuthController::class, 'redirect'])->name('login');
Route::get('/auth/discord/callback', [AuthController::class, 'callback']);

Route::middleware('auth')->group(function () {
    
    // Language Selection
    Route::get('/language', [LanguageController::class, 'index'])->name('language.select');
    Route::post('/language', [LanguageController::class, 'store'])->name('language.store');

    // Specialization Selection
    Route::get('/specialization', [SpecializationController::class, 'index'])->name('specialization.select');
    Route::post('/specialization', [SpecializationController::class, 'store'])->name('specialization.store');

    // Quiz
    Route::get('/quiz/{specialization}', [QuizController::class, 'start'])->name('quiz.start');
    Route::post('/quiz/{test}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::post('/quiz/{test}/autosave', [QuizController::class, 'autosave'])->name('quiz.autosave');

    // Results
    Route::get('/results/{test}', [ResultController::class, 'show'])->name('results.show');
    Route::get('/history', [ResultController::class, 'history'])->name('results.history');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('auth.logout');
});
