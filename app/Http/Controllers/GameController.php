<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Services\GameService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class GameController extends Controller
{
    public function store(int $quiz_id, GameService $gameService): RedirectResponse
    {
        try {
            $game = $gameService->createGame($quiz_id);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()
            ->route('game.show', ['game_id' => $game->id]);
    }

    public function show(string $game_id, GameService $service): Response
    {
        $sort_array = (array) session('sort_array');
        $fifty_fifty_hint = (bool) (session('fifty_fifty_hint'));
        $show_correct_answer = (bool) (session('show_correct_answer'));
        $chosen_answer_id = (int) (session('chosen_answer_id'));

        $game = Game::with('quiz', 'question.answers.tmdb_image', 'question.tmdb_image', 'latestStep')->find($game_id);
        if ($game->show_correct_answer && $show_correct_answer === false) {
            $show_correct_answer = true;
        }

        $game_data = $service->show(
            $game,
            $sort_array,
            $fifty_fifty_hint,
            $show_correct_answer
        );

        $countDown = ceil($game->quiz->timer_count - $game->updated_at->diffInSeconds(now()));
        if ($game_data['error'] !== '' || $game_data['message'] !== '' || $show_correct_answer) {
            $countDown = 0;
        }

        $currentStepIndex = array_search($game->latestStep?->question_id, $game->question_row);

        return Inertia::render('Game/ShowGame', [
            'game' => new GameResource($game),
            'answers' => $game_data['answers'],
            'currentStep' => $currentStepIndex !== false ? $currentStepIndex + 1 : 0,
            'questionsCount' => count($game->question_row),
            'error' => $game_data['error'],
            'message' => $game_data['message'],
            'correct_answer_id' => $game_data['correct_answer_id'],
            'firstQuestion' => $game_data['firstQuestion'],
            'countDown' => $countDown,
            'tmdbImage' => $game_data['tmdb_image'],
            'quizLikeInfo' => $game_data['quiz_like_info'],
            'show_correct_answer' => $show_correct_answer,
            'chosen_answer_id' => $chosen_answer_id,
        ]);
    }

    public function edit(string $game_id, Request $request, GameService $service): RedirectResponse
    {
        $sort_array = $request->array('sort_array');
        $fifty_fifty_hint = $request->boolean('fifty_fifty_hint');
        $can_skip = $request->boolean('can_skip');
        $next = $request->boolean('next');
        $chosen_answer_id = $request->integer('answer_id');

        $game = Game::with('quiz', 'question.answers', 'latestStep')->find($game_id);
        if ($game->show_correct_answer === true && $next === true) {
            $game->show_correct_answer = false;
            $game->save();
        }
        $error = $game->latestStep !== null ? $service->getError($game) : '';
        abort_if(
            ($fifty_fifty_hint && $game->fifty_fifty_hint === false) || ($can_skip && $game->can_skip === false) || ($error === '' || $error === '0') === false || $game->show_correct_answer === true,
            400
        );

        $result = $service->processAnswer(
            $chosen_answer_id,
            $game,
            $fifty_fifty_hint,
            $can_skip,
            $next
        );

        return redirect()
            ->route('game.show', ['game_id' => $game_id])->with([
                'sort_array' => $sort_array,
                'fifty_fifty_hint' => $fifty_fifty_hint,
                'show_correct_answer' => $result['show_correct_answer'] ?? false,
                'chosen_answer_id' => $chosen_answer_id,
            ]);
    }

    public function setUpdate(string $game_id, GameService $service): array
    {
        $game = Game::with('latestStep')->find($game_id);

        return [
            'status' => $service->setUpdateTime($game) ? 'updated' : 'not_updated',
        ];
    }
}
