<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Jobs\SyncExternalDataJob;
use App\Services\PostFetchService;
use Illuminate\Support\Facades\Cache;
use App\Contracts\PostFetchServiceInterface;

class PostController extends Controller
{
    public function __construct(PostFetchServiceInterface $postFetchService) {}

    public function index()
    {
        // Use paginate to handle large datasets on the UI
        $posts = Post::latest()->paginate(15);
        return view('posts.index', compact('posts'));
    }

    public function triggerSync()
    {
        // Use job for large data datasets
        try {
            SyncExternalDataJob::dispatch();
            return response()->json(['status' => 'processing']);
        } catch (\RuntimeException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Service Unavailable: ' . $e->getMessage()
            ], 503); // Service Unavailable status
        }
    }
}
