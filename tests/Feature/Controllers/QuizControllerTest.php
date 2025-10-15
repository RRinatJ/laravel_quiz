<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_quiz_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Quiz::factory()->count(3)->create();
        $response = $this->actingAs($user)->get(route('quiz.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Quiz/QuizList')
            ->has('quizzes.data', 3)
            ->has('message')
            ->has('error'));
    }

    public function test_quiz_create(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('quiz.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Quiz/QuizForm'));
    }

    public function test_quiz_store_validation_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('quiz.store'), [
            'title' => '',
            'is_work' => 'not-a-boolean',
            'timer_count' => -5,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['title', 'is_work', 'timer_count']);
    }

    public function test_quiz_store_success(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('quiz.store'), [
            'title' => 'Sample Quiz',
            'is_work' => true,
            'timer_count' => 10,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quiz.index'));
        $this->assertDatabaseHas('quizzes', ['title' => 'Sample Quiz']);
    }

    public function test_quiz_edit(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();
        $response = $this->actingAs($user)->get(route('quiz.edit', $quiz->id));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Quiz/QuizForm')
            ->has('quiz')
            ->has('message'));
    }

    public function test_quiz_update_validation_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();
        $response = $this->actingAs($user)->post(route('quiz.update', $quiz->id), [
            'title' => '',
            'is_work' => 'not-a-boolean',
            'timer_count' => -5,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['title', 'is_work', 'timer_count']);
    }

    public function test_quiz_update_success(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();
        $response = $this->actingAs($user)->post(route('quiz.update', $quiz->id), [
            'title' => 'Updated Quiz',
            'is_work' => false,
            'timer_count' => 15,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quiz.edit', $quiz->id));
        $this->assertDatabaseHas('quizzes', ['id' => $quiz->id, 'title' => 'Updated Quiz']);
    }

    public function test_quiz_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $quiz = Quiz::factory()->create();
        $response = $this->actingAs($user)->delete(route('quiz.destroy', $quiz->id));

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quiz.index'));
        $this->assertDatabaseMissing('quizzes', ['id' => $quiz->id]);
    }

    public function test_quiz_routes_require_authentication(): void
    {
        $quiz = Quiz::factory()->create();

        $this->get(route('quiz.index'))->assertRedirect(route('login'));
        $this->get(route('quiz.create'))->assertRedirect(route('login'));
        $this->post(route('quiz.store'), [])->assertRedirect(route('login'));
        $this->get(route('quiz.edit', $quiz->id))->assertRedirect(route('login'));
        $this->post(route('quiz.update', $quiz->id), [])->assertRedirect(route('login'));
        $this->delete(route('quiz.destroy', $quiz->id))->assertRedirect(route('login'));
    }
}
