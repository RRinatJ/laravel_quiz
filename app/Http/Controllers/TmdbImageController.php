<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Tmdb\FactoryServiceInterface;
use App\Http\Requests\TmdbImageSearchRequest;
use App\Http\Requests\TmdbImagesRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class TmdbImageController extends Controller
{
    public function __construct(private readonly FactoryServiceInterface $service
    ) {}

    public function search(TmdbImageSearchRequest $request): JsonResponse
    {
        $type = $request->string('type');
        $query = $request->string('query');
        try {
            $type_api = $this->service->init($type->toString());

            return response()->json($type_api->search($query->toString()));
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'error' => [$e->getMessage()],
                ],
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    public function images(TmdbImagesRequest $request): JsonResponse
    {
        $type = $request->string('type');
        $tmdb_id = $request->integer('tmdb_id');
        try {
            $type_api = $this->service->init($type->toString());

            return response()->json($type_api->images($tmdb_id));
        } catch (Exception $exception) {
            return response()->json([
                'tmdb_image' => $exception->getMessage(),
            ], 401);
        }
    }
}
