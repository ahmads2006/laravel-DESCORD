<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Discord OAuth routes (need to be in web for redirects)
Route::get('/auth/discord', [AuthController::class, 'redirect'])->name('auth.discord');
Route::get('/auth/discord/callback', [AuthController::class, 'callback'])->name('auth.discord.callback');

Route::get('/', function () {
    return response()->json(['message' => 'Backend API is running.']);
});
