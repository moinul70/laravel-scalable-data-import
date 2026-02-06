<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Contracts\PostFetchServiceInterface;

class PostFetchService implements PostFetchServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function fetch(): Collection
    {
        $cacheKey = 'api_circuit_breaker_posts';
        $threshold = 3; // Number of failures before opening the circuit
        $timeoutSeconds = 10; // How long to stay "Open" before trying again

        // Check if the Circuit is "Open"
        if (Cache::get($cacheKey . '_status') === 'open') {
            throw new \RuntimeException('Circuit is open: API is currently unreachable. Please try again later.');
        }

        try {
            $response = Http::timeout(10) // Reduced timeout for better responsiveness
                ->retry(2, 100)
                ->get(env("EXTERNAL_POST_API"));

            if ($response->successful()) {
                // Success: Reset failure count
                Cache::forget($cacheKey . '_failures');
                return collect($response->json());
            }

            throw new \Exception('API Error');
        } catch (\Exception $e) {
            // Increment Failure Count
            $failures = Cache::increment($cacheKey . '_failures');
            Log::warning(['api_failed_count' => $failures]);
            Cache::put('sync_status', 'Unavailable', 300);
            if ($failures >= $threshold) {
                // Trip the Circuit (Open it)
                Cache::put($cacheKey . '_status', 'open', $timeoutSeconds);
                throw new \RuntimeException('Circuit tripped: API failed multiple times and is now locked.');
            }

            throw new \RuntimeException('Post API fetch failed: ' . $e->getMessage());
        }
    }


    public function store(Collection $data): void
    {
        try {
            $data->chunk(10)->each(function (Collection $chunk) {
                Post::upsert(
                    $chunk->map(fn($item) => [
                        'external_id' => $item['id'],
                        'user_id'     => $item['userId'],
                        'title'       => $item['title'],
                        'body'        => $item['body'],
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ])->toArray(),
                    ['external_id'],
                    ['title', 'body', 'updated_at']
                );
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function syncPosts(): void
    {
        Cache::put('sync_status', 'processing', 300); // Expires in 5 mins safety net

        $data = $this->fetch();
        $this->store($data);

        Cache::put('sync_status', 'finished', 300);
    }
}
