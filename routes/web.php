<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Aws\DynamoDb\Marshaler;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\DashboardController;




Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard',
    [DashboardController::class, 'index']
)->middleware(['auth'])->name('dashboard');

Route::post('subscribe',
    [MusicController::class, 'subscribe']
)->middleware(['auth'])->name('subscribe');

Route::post('unsubscribe',
    [MusicController::class, 'unsubscribe']
)->middleware(['auth'])->name('unsubscribe');


Route::get('/subscription', [MusicController::class, 'subscription'])->middleware(['auth'])->name('subscription');

Route::get('/query', [MusicController::class, 'query'])->middleware(['auth'])->name('query');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
