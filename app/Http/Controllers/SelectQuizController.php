<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\SelectQuizResource;
use App\Models\Game;
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
        $popular = $request->boolean('popular');
        $sort_by_likes = $request->boolean('sort_by_likes');

        $quizzes = Quiz::query()
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
            ->when($popular, function ($query): void {
                $query->withCount('games')
                    ->orderBy('games_count', 'desc');
            })
            ->when($sort_by_likes, function ($query): void {
                $query->orderBy('likes_count', 'desc');
            })
            ->paginate(10)
            ->withQueryString();

        $quizzes_ids = $quizzes->pluck('id')->toArray();

        $games_result = collect();
        if (Auth::check() && count($quizzes_ids) > 0) {
            $games_result = Game::query()
                ->selectRaw('quiz_id, correct_count, MAX(created_at) AS created_at')
                ->where('user_id', Auth::id())
                ->whereIn('quiz_id', $quizzes_ids)
                ->groupBy('quiz_id')
                ->get()
                ->keyBy('quiz_id');
        }

        return Inertia::render('SelectQuiz', [
            'quizzes' => SelectQuizResource::collection(
                $quizzes
            ),
            'games_result' => $games_result,
            'filters' => [
                'quiz_title' => $quiz_title,
                'liked' => $liked,
                'popular' => $popular,
                'sort_by_likes' => $sort_by_likes,
            ],
        ]);
    }
}
