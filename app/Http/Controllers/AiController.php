<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AiGetQuestionRequest;
use App\Services\AiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class AiController extends Controller
{
    public function __construct(private readonly AiService $service) {}

    public function get_question(AiGetQuestionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $theme = $validated['theme'];
        $number_of_options = (int) $validated['number_of_options'];

        try {
            return response()->json($this->service->getQuestion($theme, $number_of_options));
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'error' => [$e->getMessage()],
                ],
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
