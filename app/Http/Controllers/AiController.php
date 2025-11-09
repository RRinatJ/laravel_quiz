<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AiGetQuestionRequest;
use App\Services\AiService;

final class AiController extends Controller
{
    public function __construct(private readonly AiService $service) {}

    public function get_question(AiGetQuestionRequest $request): array
    {
        $validated = $request->validated();

        $theme = $validated['theme'];
        $number_of_options = (int) $validated['number_of_options'];

        return $this->service->getQuestion($theme, $number_of_options);
    }
}
