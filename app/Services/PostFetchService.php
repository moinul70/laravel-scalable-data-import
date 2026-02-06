<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
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
        $data->chunk(500)->each(function (Collection $chunk) {
            Post::upsert(
                $chunk->map(fn ($item) => [
                    'external_id' => $item['id'],
                    'title'       => $item['title'],
                    'body'        => $item['body'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ])->toArray(),
                ['external_id','user_id'],
                ['title', 'body', 'updated_at']
            );
        });
    }
}
