<?php

declare(strict_types=1);

namespace App\Services\Tmdb;

use App\Contracts\Tmdb\ApiSearch;
use Exception;
use Illuminate\Http\Client\PendingRequest;

final readonly class TvService implements ApiSearch
{
    public function __construct(private PendingRequest $client
    ) {}

    public function search(string $query): array|string
    {
        $response = $this->client
            ->withQueryParameters([
                'query' => $query,
                'include_adult' => false,
                'page' => 1,
            ])->get(config('services.tmdb.urls.tv_search'));
        throw_if($response->failed(), new Exception('Tmdb return error HTTP-status: '.$response->status()));

        return $response->json();
    }

    public function images(int $series_id): array|string
    {
        $response = $this->client
            ->withUrlParameters([
                'series_id' => $series_id,
            ])->get(config('services.tmdb.urls.tv_images_by_id'));
        throw_if($response->failed(), new Exception('Tmdb return error HTTP-status: '.$response->status()));

        return $response->json();
    }
}
