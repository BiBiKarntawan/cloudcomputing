<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Aws\DynamoDb\Marshaler;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\HandleAppearance;


Route::get('/api/check-auth', function() {
    if (!Auth::check()) {
        return response()->json(['authenticated' => false], 401);
    }
    return response()->json(['authenticated' => true]);
})->middleware('web');

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard',
    [MusicController::class, 'index']
)->middleware(['auth', HandleAppearance::class])->name('dashboard');

Route::post('subscribe',
    [MusicController::class, 'subscribe']
)->middleware(['auth', HandleAppearance::class])->name('subscribe');

Route::post('unsubscribe',
    [MusicController::class, 'unsubscribe']
)->middleware(['auth', HandleAppearance::class])->name('unsubscribe');


Route::get('/subscription', [MusicController::class, 'subscription'])->middleware(['auth', HandleAppearance::class])->name('subscription');

Route::get('/query', [MusicController::class, 'query'])->middleware(['auth', HandleAppearance::class])->name('query');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
