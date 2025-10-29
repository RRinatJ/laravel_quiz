<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class GameController extends Controller
{
    public function store(int $quiz_id, GameService $gameService): RedirectResponse
    {
        $game = $gameService->createGame($quiz_id);

        return redirect()
            ->route('game.show', ['game_id' => $game->id]);
    }

    public function show($game_id, GameService $service): Response
    {
        $sort_array = (array) session('sort_array');
        $fifty_fifty_hint = (bool) (session('fifty_fifty_hint'));

        $game = Game::with('quiz', 'question.answers', 'latestStep')->find($game_id);

        $game_data = $service->show(
            $game,
            $sort_array,
            $fifty_fifty_hint
        );

        $countDown = ceil($game->quiz->timer_count - $game->updated_at->diffInSeconds(now()));
        if ($game_data['error'] !== '' || $game_data['message'] !== '') {
            $countDown = 0;
        }

        return Inertia::render('Game/ShowGame', [
            'game' => new GameResource($game),
            'answers' => $game_data['answers'],
            'questionsCount' => count($game->question_row),
            'error' => $game_data['error'],
            'message' => $game_data['message'],
            'correct_answer_id' => $game_data['correct_answer_id'],
            'firstQuestion' => $game_data['firstQuestion'],
            'countDown' => $countDown,
        ]);
    }

    public function edit($game_id, Request $request, GameService $service): RedirectResponse
    {
        $sort_array = $request->array('sort_array');
        $fifty_fifty_hint = $request->boolean('fifty_fifty_hint');
        $can_skip = $request->boolean('can_skip');
        $service->processAnswer(
            $request->integer('answer_id'),
            Game::with('quiz', 'question.answers')->find($game_id),
            $fifty_fifty_hint,
            $can_skip
        );

        return redirect()
            ->route('game.show', ['game_id' => $game_id])->with([
                'sort_array' => $sort_array,
                'fifty_fifty_hint' => $fifty_fifty_hint,
            ]);
    }
}
