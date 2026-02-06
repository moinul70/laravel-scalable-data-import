<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface PostFetchServiceInterface
{
    public function fetch(): Collection;

    public function store(Collection $data): void;
}
