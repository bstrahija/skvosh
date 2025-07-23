<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Homepage');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Sports navigation routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('your-match', function () {
        return Inertia::render('YourMatch');
    })->name('your-match');

    Route::get('live-games', function () {
        return Inertia::render('LiveGames');
    })->name('live-games');

    Route::get('all-games', function () {
        return Inertia::render('AllGames');
    })->name('all-games');

    Route::get('videos', function () {
        return Inertia::render('Videos');
    })->name('videos');

    Route::get('categories', function () {
        return Inertia::render('Categories');
    })->name('categories');

    Route::get('score', function () {
        return Inertia::render('Score');
    })->name('score');

    Route::get('statistics', function () {
        return Inertia::render('Statistics');
    })->name('statistics');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
