<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Services\QuestionService;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __invoke(QuestionService $questionService): Response
    {
        $latestQuestions = $questionService->getLatestQuestions();

        return Inertia::render('Dashboard', [
            'latestQuestions' => $latestQuestions,
            'latestQuizzes' => Quiz::query()
                ->select('id', 'title', 'image', 'is_work', 'created_at')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($item) {
                    $arr = $item->toArray();
                    $arr['created_at'] = $item->created_at->diffForHumans();

                    return $arr;
                }),
        ]);
    }
}
