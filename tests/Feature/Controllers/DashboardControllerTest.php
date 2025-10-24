<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Enums\UserRole;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_admin(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Quiz::factory()->has(Question::factory()->count(2))->count(3)->create();
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Dashboard')
            ->has('latestQuestions', 5)
            ->has('latestQuizzes', 3)
            ->has('latestGames', 0)
        );
    }

    public function test_dashboard_player(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $quizzes = Quiz::factory()->has(Question::factory()->count(2))->count(3)->create();
        $quiz = $quizzes->first();
        $quiz->is_work = true;
        $quiz->save();
        $response = $this->actingAs($user)->get(route('game.create', [
            'quiz_id' => $quiz->id,
        ]));

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Dashboard')
            ->has('latestQuestions', 0)
            ->has('latestQuizzes', $quizzes->where('is_work', true)->count())
            ->has('latestGames', 1)
        );
    }
}
