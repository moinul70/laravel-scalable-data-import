<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\PostFetchServiceInterface;

class SyncExternalDataJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PostFetchServiceInterface $postFetchService)
    {
        $postFetchService->syncPosts();
    }
}
