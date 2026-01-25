<?php

declare(strict_types=1);

namespace App\Contracts\Tmdb;

interface FactoryServiceInterface
{
    public function __construct();

    public function init(string $type): ApiSearch;
}
