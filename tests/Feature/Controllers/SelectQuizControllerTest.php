<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Enums\UserRole;
use App\Models\Game;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class SelectQuizControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_select_quiz_index(): void
    {
        Quiz::factory()
            ->has(Question::factory()->count(2))
            ->count(3)
            ->create(['is_work' => true]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('quizzes.data', 3)
            ->has('filters')
            ->where('filters.quiz_title', '')
            ->where('filters.liked', false)
            ->where('filters.popular', false)
            ->where('filters.sort_by_likes', false)
        );
    }

    public function test_select_quiz_only_shows_working_quizzes(): void
    {
        Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true]);

        Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => false]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('quizzes.data', 1)
        );
    }

    public function test_select_quiz_excludes_quizzes_without_questions(): void
    {
        Quiz::factory()->create(['is_work' => true]);

        Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('quizzes.data', 1)
        );
    }

    public function test_select_quiz_search_by_title(): void
    {
        Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true, 'title' => 'Laravel Quiz']);

        Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true, 'title' => 'PHP Quiz']);

        $response = $this->get(route('home', ['quiz_title' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('quizzes.data', 1)
            ->has('quizzes.data.0', fn (Assert $quiz): Assert => $quiz
                ->where('title', 'Laravel Quiz')
                ->etc()
            )
            ->where('filters.quiz_title', 'Laravel')
        );
    }

    public function test_select_quiz_filter_by_liked(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::PLAYER]);

        $liked_quiz = Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true]);

        Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true]);

        $liked_quiz->like($user);

        $response = $this->actingAs($user)->get(route('home', ['liked' => true]));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('quizzes.data', 1)
            ->has('quizzes.data.0', fn (Assert $quiz): Assert => $quiz
                ->where('id', $liked_quiz->id)
                ->etc()
            )
            ->where('filters.liked', true)
        );
    }

    public function test_select_quiz_filter_by_popular(): void
    {
        $popular_quiz = Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true]);

        $less_popular_quiz = Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true]);

        /** @var User $user */
        $user = User::factory()->create();

        Game::factory()->count(5)->forQuiz($popular_quiz)->forUser($user)->create();
        Game::factory()->count(2)->forQuiz($less_popular_quiz)->forUser($user)->create();

        $response = $this->get(route('home', ['popular' => true]));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('quizzes.data', 2)
            ->has('quizzes.data.0', fn (Assert $quiz): Assert => $quiz
                ->where('id', $popular_quiz->id)
                ->etc()
            )
            ->where('filters.popular', true)
        );
    }

    public function test_select_quiz_sort_by_likes(): void
    {
        $more_liked_quiz = Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true]);

        Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true]);

        /** @var User $user */
        $user = User::factory()->create();

        $more_liked_quiz->like($user);

        $response = $this->get(route('home', ['sort_by_likes' => true]));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('quizzes.data', 2)
            ->has('quizzes.data.0', fn (Assert $quiz): Assert => $quiz
                ->where('id', $more_liked_quiz->id)
                ->etc()
            )
            ->where('filters.sort_by_likes', true)
        );
    }

    public function test_select_quiz_with_games_result_for_authenticated_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::PLAYER]);

        $quiz = Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true]);

        Game::factory()->forQuiz($quiz)->forUser($user)->create([
            'correct_count' => 5,
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('games_result', 1)
        );
    }

    public function test_select_quiz_without_games_result_for_guest(): void
    {
        Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('games_result', 0)
        );
    }

    public function test_select_quiz_pagination(): void
    {
        Quiz::factory()
            ->has(Question::factory()->count(2))
            ->count(11)
            ->create(['is_work' => true]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('quizzes.data', 10)
        );

        $response = $this->get(route('home', ['page' => 2]));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('quizzes.data', 1)
        );
    }

    public function test_select_quiz_combined_filters(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::PLAYER]);

        $quiz = Quiz::factory()
            ->has(Question::factory()->count(2))
            ->create(['is_work' => true, 'title' => 'Test Quiz']);

        $quiz->like($user);

        /** @var User $another_user */
        $another_user = User::factory()->create();

        Game::factory()->count(3)->forQuiz($quiz)->forUser($another_user)->create();

        $response = $this->actingAs($user)->get(route('home', [
            'quiz_title' => 'Test',
            'liked' => true,
            'popular' => true,
            'sort_by_likes' => true,
        ]));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('SelectQuiz')
            ->has('quizzes.data', 1)
            ->where('filters.quiz_title', 'Test')
            ->where('filters.liked', true)
            ->where('filters.popular', true)
            ->where('filters.sort_by_likes', true)
        );
    }
}
