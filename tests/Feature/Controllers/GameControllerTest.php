<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Answer;
use App\Models\Game;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_select_game(): void
    {
        $quizzes = Quiz::factory()->has(Question::factory()->count(4))->count(3)->create();
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('quizzes', $quizzes->where('is_work', true)->count())
        );
    }

    public function test_create_game(): void
    {
        $quiz = Quiz::factory()->has(Question::factory()->count(1))->create();
        $quiz->is_work = true;
        $quiz->save();

        $response = $this->get(route('game.create', [
            'quiz_id' => $quiz->id,
        ]));
        $response->assertStatus(302);
        $this->assertDatabaseHas('games', ['quiz_id' => $quiz->id, 'correct_count' => 0]);
    }

    public function test_show_game_not_found(): void
    {
        app()->detectEnvironment(fn (): string => 'production');
        $response = $this->get(route('game.show', [
            'game_id' => 999,
        ]));
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('ErrorPage')
            ->where('status', 500)
        );
    }

    public function test_show_game(): void
    {
        $quiz = Quiz::factory()->has(Question::factory()->has(Answer::factory()->count(2))->count(2))->create();
        $quiz->is_work = true;
        $quiz->save();

        $this->get(route('game.create', [
            'quiz_id' => $quiz->id,
        ]));
        $game = Game::query()->where('quiz_id', $quiz->id)->first();

        $response = $this->get(route('game.show', [
            'game_id' => $game->id,
        ]));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Game/ShowGame')
            ->where('game.id', $game->id)
            ->where('game.quiz.id', $quiz->id)
            ->where('firstQuestion', true)
            ->has('answers', 2)
            ->has('message')
            ->has('error'));
    }

    public function test_edit_game(): void
    {
        $quiz = Quiz::factory()->has(Question::factory()->has(Answer::factory()->count(2))->count(2))->create();
        $quiz->is_work = true;
        $quiz->save();

        $this->get(route('game.create', [
            'quiz_id' => $quiz->id,
        ]));

        $game = Game::with('question.answers')->where('quiz_id', $quiz->id)->first();
        $answer = $game->question->answers->first();
        $response = $this->post(route('game.edit', [
            'game_id' => $game->id,
            'sort_array' => [],
            'answer_id' => $answer->id,
        ]));
        $response->assertStatus(302);
        $this->assertDatabaseHas('game_steps', ['game_id' => $game->id, 'is_correct' => $answer->is_correct]);
    }
}
