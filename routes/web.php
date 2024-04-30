<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route de test pour controler la communication avec le serveur
Route::get('/test', function() {
    return response()->json([
        'result' => 'hello test'
    ]);
});

// Route get de tous les événements en cours
Route::get('/event', [EventController::class, 'index']);

// Route get d'un événements en particulier
Route::get('/event/detail/{id}', [EventController::class, 'show']);

// Route post d'un événement
Route::post('/event/create', [EventController::class, 'store']);

// Route de mis a jour d'un evenement
Route::put('/event/update/{id}', [EventController::class, 'update']);

// Route de suppression d'un événement
Route::delete('/event/delete/{id}', [EventController::class, 'destroy']);

require __DIR__.'/auth.php';
