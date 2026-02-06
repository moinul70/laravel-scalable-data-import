<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

//get the current posts
Route::get('/posts', [PostController::class, 'index']);
//sync the posts from external api
Route::post('/sync', [PostController::class, 'triggerSync'])->name('posts.sync');

Route::get('/sync-status', function () {
    return response()->json([
        'status' => Cache::get('sync_status', 'idle')
    ]);
});

