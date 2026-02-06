<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Collection;
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
        $response = Http::timeout(30)
            ->retry(3, 200)
            ->get('https://jsonplaceholder.typicode.com/posts');

        if (! $response->successful()) {
            throw new \RuntimeException('Post API fetch failed');
        }

        return collect($response->json());
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
