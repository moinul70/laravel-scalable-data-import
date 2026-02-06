<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SyncController;

Route::controller(PostController::class)
    ->group(function () {
        // Get current posts
        Route::get('/', 'index')->name('posts.index');
});

Route::controller(SyncController::class)
    ->group(function () {

        // Sync status
        Route::get('/sync-status', 'syncStatus')
            ->name('posts.sync.status')
            ->middleware('throttle:10,1'); // max 10 requests per minute

        // Sync posts from external API
        Route::post('/sync', 'triggerSync')
            ->name('posts.sync')
            ->middleware('throttle:4,1'); // max 4 requests per minute
});
