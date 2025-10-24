<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Quiz;
use App\Models\User;
use App\Services\GameService;
use App\Services\QuestionService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __invoke(QuestionService $questionService, GameService $gameService, #[CurrentUser] User $user): Response
    {

        $latestQuestions = $user->checkRole(UserRole::ADMIN) ? $questionService->getLatestQuestions() : [];
        $latestGames = $user->checkRole(UserRole::PLAYER) ? $gameService->getLatestGames($user->id) : [];
        $latesQuizzes = Quiz::query()
            ->when($user->checkRole(UserRole::PLAYER), function ($query): void {
                $query->where('is_work', true);
            })
            ->select('id', 'title', 'image', 'is_work', 'created_at')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($item) {
                $arr = $item->toArray();
                $arr['created_at'] = $item->created_at->diffForHumans();
                $arr['image'] = $item->image ? config('app.url').Storage::url($item->image) : null;

                return $arr;
            });

        return Inertia::render('Dashboard', [
            'latestQuestions' => $latestQuestions,
            'latestQuizzes' => $latesQuizzes,
            'latestGames' => $latestGames,
        ]);
    }
}
