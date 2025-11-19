<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Enums\UserRole;
use App\Models\Answer;
use App\Models\Game;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports(): void
    {
        $quiz = Quiz::factory()->has(Question::factory()->has(Answer::factory()->count(2))->count(1))->create([
            'is_work' => true,
        ]);
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

        $params = [
            'start' => now()->subDay()->toDateString(),
            'end' => now()->toDateString(),
        ];
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $response = $this->actingAs($user)->get(route('reports.popular_quizzes', $params));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('reports/PopularQuizzesReport')
            ->has('filters', 2)
            ->has('data', 1)
            ->has('data.0', fn (Assert $item): Assert => $item
                ->where('quiz_id', $quiz->id)
                ->where('play_count', 1)
                ->where('win_count', $answer->is_correct ? 1 : 0)
                ->etc()
            )
        );

        $response = $this->actingAs($user)->get(route('reports.questions_report', $params));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('reports/QuestionsReport')
            ->has('filters', 2)
            ->has('data', 1)
            ->has('data.0', fn (Assert $item): Assert => $item
                ->where('play_count', 1)
                ->where('corrected_count', $answer->is_correct ? 1 : 0)
                ->etc()
            )
        );
    }

    public function test_reports_no_data(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $response = $this->actingAs($user)->get(route('reports.popular_quizzes'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('reports/PopularQuizzesReport')
            ->has('filters', 0)
            ->has('data', 0)
        );

        $response = $this->actingAs($user)->get(route('reports.questions_report'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('reports/QuestionsReport')
            ->has('filters', 0)
            ->has('data', 0)
        );
    }

    public function test_requests_validation_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $response = $this->actingAs($user)->get(route('reports.popular_quizzes', [
            'start' => 'invalid-date',
            'end' => '2024-01-01',
        ]));
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['start']);

        $response = $this->actingAs($user)->get(route('reports.questions_report', [
            'start' => '2024-01-01',
            'end' => 'invalid-date',
        ]));
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['end']);
    }

    public function test_reports_access_denied_for_non_admin(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::PLAYER]);
        $response = $this->actingAs($user)->get(route('reports.popular_quizzes'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get(route('reports.questions_report'));
        $response->assertStatus(403);
    }

    public function test_reports_access_denied_for_guest(): void
    {
        $response = $this->get(route('reports.popular_quizzes'));
        $response->assertStatus(302);

        $response = $this->get(route('reports.questions_report'));
        $response->assertStatus(302);
    }
}
