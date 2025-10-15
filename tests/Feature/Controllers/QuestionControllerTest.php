<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class QuestionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_question_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Question::factory()->has(Answer::factory()->count(4))->count(3)->create();
        $response = $this->actingAs($user)->get(route('question.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Question/QuestionList')
            ->has('questions.data', 3)
            ->has('questions.data.0', fn (Assert $question): Assert => $question
                ->where('id', Question::query()->first()->id)
                ->has('answers', 4)
                ->etc()
            )
            ->has('message')
            ->has('error'));
    }

    public function test_question_create(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('question.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Question/QuestionForm'));
    }

    public function test_question_store_validation_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('question.store'), [
            'question' => '',
            'answers' => [],
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['question', 'answers']);
    }

    public function test_question_store_success(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('question.store'), [
            'question' => 'Sample Question',
            'answers' => [
                ['id' => '111', 'text' => 'Answer 1', 'is_correct' => false],
                ['id' => '222', 'text' => 'Answer 2', 'is_correct' => true],
                ['id' => '333', 'text' => 'Answer 3', 'is_correct' => false],
                ['id' => '444', 'text' => 'Answer 4', 'is_correct' => false],
            ],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('question.index'));
        $this->assertDatabaseHas('questions', ['question' => 'Sample Question']);
    }

    public function test_question_edit(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $question = Question::factory()->has(Answer::factory()->count(4))->create();
        $response = $this->actingAs($user)->get(route('question.edit', $question->id));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Question/QuestionForm')
            ->has('question')
            ->has('message'));
    }

    public function test_question_update_validation_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $question = Question::factory()->has(Answer::factory()->count(4))->create();
        $response = $this->actingAs($user)->post(route('question.update', $question->id), [
            'question' => '',
            'answers' => [],
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['question', 'answers']);
    }

    public function test_question_update_success(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $question = Question::factory()->has(Answer::factory()->count(4))->create();

        $response = $this->actingAs($user)->post(route('question.update', $question->id), [
            'question' => 'Updated Question',
            'answers' => $question->answers->toArray(),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('question.edit', $question->id));
        $this->assertDatabaseHas('questions', ['id' => $question->id, 'question' => 'Updated Question']);
        $this->assertDatabaseHas('answers', ['question_id' => $question->id, 'text' => $question->answers->first()->text]);
    }

    public function test_question_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $question = Question::factory()->has(Answer::factory()->count(4))->create();
        $response = $this->actingAs($user)->delete(route('question.destroy', $question->id));

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('question.index'));
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }

    public function test_question_routes_require_authentication(): void
    {
        $question = Question::factory()->has(Answer::factory()->count(4))->create();

        $this->get(route('question.index'))->assertRedirect(route('login'));
        $this->get(route('question.create'))->assertRedirect(route('login'));
        $this->post(route('question.store'), [])->assertRedirect(route('login'));
        $this->get(route('question.edit', $question->id))->assertRedirect(route('login'));
        $this->post(route('question.update', $question->id), [])->assertRedirect(route('login'));
        $this->delete(route('question.destroy', $question->id))->assertRedirect(route('login'));
    }
}
