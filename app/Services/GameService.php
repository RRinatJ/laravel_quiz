<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Game;
use App\Models\GameStep;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final readonly class GameService
{
    public function createGame(int $quiz_id, ?int $chat_id = null): Game
    {
        $quiz = Quiz::with('questions:id')->where('is_work', 1)->find($quiz_id);
        $question_row = $quiz->questions->shuffle()->pluck('id')->toArray();

        return Game::query()->create([
            'current_question_id' => $question_row[0],
            'quiz_id' => $quiz->id,
            'correct_count' => 0,
            'question_row' => $question_row,
            'user_id' => Auth::id(),
            'fifty_fifty_hint' => $quiz->fifty_fifty_hint,
            'can_skip' => $quiz->can_skip,
            'chat_id' => $chat_id,
        ]);
    }

    public function show(Game $game, array $sort_array = [], bool $fifty_fifty_hint = false): array
    {
        $firstQuestion = $game->latestStep === null;

        $error = $firstQuestion ? '' : $this->getError($game);
        $message = $firstQuestion ? '' : $this->getMessage($game, $error);

        $correct_answer_id = $game->question->answers->where('is_correct', true)->first()?->id;
        $answers = $this->getAnswers($game, $error, $sort_array, $correct_answer_id, $fifty_fifty_hint);
        if ($error === '') {
            $correct_answer_id = null;
        }
        $tmdb_image = (bool) $game->question->tmdb_image;

        return [
            'answers' => $answers,
            'error' => $error,
            'message' => $message,
            'correct_answer_id' => $correct_answer_id,
            'firstQuestion' => $game->latestStep === null,
            'tmdb_image' => $tmdb_image,
        ];
    }

    public function processAnswer(int $given_answer, Game $game, bool $fifty_fifty_hint, bool $can_skip): void
    {
        if ($fifty_fifty_hint) {
            $this->processFiftyFiftyHint($game);

            return;
        }
        $chosen_answer_id = $this->getAnswerId($given_answer);
        $is_correct = $this->checkIsCorrectAnswer($game, $chosen_answer_id);
        $times_out = $this->checkTimesOut($game);

        $game_step = GameStep::query()->create([
            'game_id' => $game->id,
            'question_id' => $game->question->id,
            'answer_id' => $chosen_answer_id,
            'times_out' => $times_out,
            'is_correct' => $is_correct,
            'fifty_fifty_hint' => $fifty_fifty_hint,
            'can_skip' => $can_skip,
        ]);

        if ($times_out) {
            return;
        }
        if ($game_step->is_correct || $game_step->can_skip) {
            $this->processCorrectQuestion($game, $game_step);
        }
    }

    public function processFiftyFiftyHint(Game $game): void
    {
        $game->fifty_fifty_hint = false;
        $game->save();
        $game->latestStep()->update(['fifty_fifty_hint' => true]);
    }

    public function getError(Game $game): string
    {
        return match (true) {
            $game->latestStep->times_out => 'Times is out!',
            $game->latestStep->is_correct === false && $game->latestStep->can_skip === false => 'Error answer!',
            default => ''
        };
    }

    public function getMessage(Game $game, string $error): string
    {
        if ($error === '' &&
            $game->current_question_id === $game->latestStep->question_id
        ) {
            return 'Congratulations! You answered all questions!';
        }

        return '';
    }

    public function getAnswers(Game $game, string $error, array $sort_array = [], ?int $correct_answer_id = null, bool $fifty_fifty_hint = false): array
    {
        $answers = $game->question->answers->shuffle()->select('id', 'text', 'image');
        if ($game->latestStep?->fifty_fifty_hint === true || $fifty_fifty_hint) {
            return $this->hiddenFiftyPrcntIncorrectAnswers($answers, $correct_answer_id);
        }

        if ($error === '') {
            return $answers->toArray();
        }

        return $this->sortAnswersByArrayIds($answers, $sort_array);
    }

    public function hiddenFiftyPrcntIncorrectAnswers($answers, $correct_answer_id): array
    {
        $count = (int) (ceil(count($answers) / 2)) - 1;
        $result = [];
        $result[] = $answers->firstWhere('id', $correct_answer_id);

        foreach ($answers as $answer) {
            if ($answer['id'] !== $correct_answer_id && $count !== 0) {
                $result[] = $answer;
                $count--;
            }
        }

        return $result;
    }

    public function sortAnswersByArrayIds($answers, $sort_array): array
    {
        if ($sort_array === []) {
            return $answers->toArray();
        }
        $result = [];
        foreach ($sort_array as $sort_item) {
            $result[] = $answers->firstWhere('id', $sort_item);
        }

        return $result;
    }

    public function checkTimesOut(Game $game): bool
    {
        $questionDurationTime = $game->updated_at->diffInSeconds(now());

        return $questionDurationTime > $game->quiz->timer_count;
    }

    public function checkIsCorrectAnswer(Game $game, ?int $chosen_answer_id): bool
    {
        $is_correct = $game->question->answers->where('is_correct', true)->where('id', $chosen_answer_id)->first();

        return $is_correct !== null;
    }

    public function getAnswerId(int $given_answer): ?int
    {
        if (is_null($given_answer)) {
            return null;
        }
        if ($given_answer === 0) {
            return null;
        }

        return $given_answer;
    }

    public function processCorrectQuestion(Game $game, GameStep $game_step): void
    {
        $current_question_id = $this->goToNextQuestion($game, $game_step->question_id);
        if ($current_question_id !== null) {
            $game->current_question_id = $current_question_id;
        }
        if ($game_step->can_skip) {
            $game->can_skip = false;
        }
        $game->correct_count++;
        $game->save();
    }

    public function goToNextQuestion(Game $game, int $current_question_id): ?int
    {
        $key = array_search($current_question_id, $game->question_row);
        $question_id = $game->question_row[$key + 1] ?? null;

        // If question was deleted
        if ($question_id !== null && Question::query()->where('id', $question_id)->doesntExist()) {
            $game->correct_count++;
            $game->save();

            return $this->goToNextQuestion($game, $question_id);
        }

        return $question_id;
    }

    public function setUpdateTime(Game $game): bool
    {
        if ($game->latestStep !== null) {
            return false;
        }

        return $game->touch();
    }

    public function getLatestGames(?int $user_id = null, ?int $count = null): Collection
    {
        if (is_null($count)) {
            $count = 5;
        }

        return Game::query()
            ->where('user_id', $user_id)
            ->select('id', 'quiz_id', 'correct_count', 'question_row', 'created_at')
            ->with('quiz:id,title')
            ->latest()
            ->take($count)
            ->get()
            ->map(function ($item) {
                $arr = $item->toArray();
                $arr['created_at'] = $item->created_at->diffForHumans();

                return $arr;
            });
    }
}
