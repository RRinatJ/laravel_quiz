<?php

declare(strict_types=1);

namespace App\Services\Tmdb;

use App\Contracts\Tmdb\ApiSearch;
use App\Contracts\Tmdb\FactoryServiceInterface;
use Exception;
use Illuminate\Support\Facades\Http;

final class FactoryService implements FactoryServiceInterface
{
    private $client;

    public function __construct()
    {
        $tmdb_api_key = config('services.tmdb.api_key');
        $env = config('app.env');

        $this->client = Http::withHeaders([
            'Authorization' => "Bearer {$tmdb_api_key}",
            'accept' => 'application/json',
        ])->withOptions([
            'verify' => $env !== 'local',
        ]);
    }

    public function init(string $type): ApiSearch
    {
        return match ($type) {
            config('services.tmdb.types.movie') => new MovieService($this->client),
            config('services.tmdb.types.tv') => new TvService($this->client),
            config('services.tmdb.types.person') => new PersonService($this->client),
            default => throw new Exception('Incorrect API type'),
        };
    }
}
