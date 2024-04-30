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

// Route de controle des événements
Route::resource('/event', EventController::class);
/* Ajout d'une nouvelle valuer a un événement existant */
Route::post('event/newdate/{id}', [EventController::class, 'newAppearance']);

require __DIR__.'/auth.php';
