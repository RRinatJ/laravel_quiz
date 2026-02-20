<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\SelectQuizResource;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final class SelectQuizController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $quiz_title = $request->string('quiz_title');
        $liked = $request->boolean('liked');

        return Inertia::render('SelectQuiz', [
            'quizzes' => SelectQuizResource::collection(
                Quiz::query()
                    ->select('id', 'title', 'description', 'image')
                    ->where('is_work', 1)
                    ->withCount('likes')
                    ->whereHas('questions')
                    ->when($quiz_title->isNotEmpty(), function ($query) use ($quiz_title): void {
                        $query->where('title', 'like', '%'.$quiz_title.'%');
                    })
                    ->when(Auth::check() && $liked, function ($query): void {
                        $query->whereHas('likes', function ($sub_query): void {
                            $sub_query->where('user_id', Auth::id());
                        });
                    })
                    ->paginate(10)
                    ->withQueryString()
            ),
            'filters' => [
                'quiz_title' => $quiz_title,
            ],
        ]);
    }
}
