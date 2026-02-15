<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\RadioController::class, 'index'])->name('home');

// Public read-only routes
Route::get('/chat/messages', [App\Http\Controllers\RadioController::class, 'getMessages']);
Route::get('/request/list', [App\Http\Controllers\RadioController::class, 'getRequests']);

// Protected routes (Login Required)
Route::middleware('auth')->group(function () {
    Route::post('/chat', [App\Http\Controllers\RadioController::class, 'sendMessage']);
    Route::post('/request', [App\Http\Controllers\RadioController::class, 'requestSong']);
    Route::post('/user/character', [App\Http\Controllers\RadioController::class, 'updateCharacter']);
});

Route::get('/api-characters', [App\Http\Controllers\RadioController::class, 'getActiveCharacters']);

// Google Auth Routes
Route::get('auth/google', [App\Http\Controllers\Auth\SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\SocialiteController::class, 'handleGoogleCallback']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
