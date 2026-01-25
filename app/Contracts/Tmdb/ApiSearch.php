<?php

declare(strict_types=1);

namespace App\Contracts\Tmdb;

use Illuminate\Http\Client\PendingRequest;

interface ApiSearch
{
    public function __construct(PendingRequest $client);

    public function search(string $query): array|string;

    public function images(int $movie_id): array|string;
}
