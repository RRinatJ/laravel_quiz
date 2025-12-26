<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class SelectQuizController extends Controller
{
    public function __invoke(Request $request, Quiz $quiz_model): Response
    {
        $quiz_title = $request->string('quiz_title');

        return Inertia::render('SelectQuiz', [
            'quizzes' => Quiz::query()
                ->select('id', 'title', 'description', 'image')
                ->where('is_work', 1)
                ->whereHas('questions')
                ->when($quiz_title->isNotEmpty(), function ($query) use ($quiz_title): void {
                    $query->where('title', 'like', '%'.$quiz_title.'%');
                })
                ->paginate(10)
                ->withQueryString(),
            'filters' => [
                'quiz_title' => $quiz_title,
            ],
        ]);
    }
}
