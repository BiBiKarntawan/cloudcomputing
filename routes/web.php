<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Aws\DynamoDb\Marshaler;
use App\Http\Controllers\MusicController;




Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard',
    [\App\Http\Controllers\DashboardController::class, 'index']
)->middleware(['auth'])->name('dashboard');

Route::post('subscribe',
    [\App\Http\Controllers\MusicController::class, 'subscribe']
)->middleware(['auth'])->name('subscribe');

Route::post('unsubscribe',
    [\App\Http\Controllers\MusicController::class, 'unsubscribe']
)->middleware(['auth'])->name('unsubscribe');

//Route::get('/dashboard',
//    [MusicController::class, 'dashboard']
//)->middleware(['auth', 'verified'])->name('dashboard');
//Route::get('/subscription',
//    [MusicController::class, 'subscription']
//)->middleware(['auth'])->name('subscription');


Route::get('/subscription', [MusicController::class, 'subscription'])->middleware(['auth'])->name('subscription');
Route::get('/dashboard', [MusicController::class, 'dashboard'])->middleware(['auth'])->name('dashboard');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
