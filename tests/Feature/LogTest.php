<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Enums\UserRole;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

final class LogTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_quiz_creation(): void
    {
        $quiz = Quiz::factory()->create(['title' => 'Test Quiz']);
        $activity = Activity::forSubject($quiz)->where('event', 'created')->first();

        $this->assertNotNull($activity);
        $this->assertEquals('default', $activity->log_name);
        $this->assertEquals('created', $activity->description);
    }

    public function test_logs_quiz_update_with_diff(): void
    {
        $quiz = Quiz::factory()->create(['title' => 'Old Title', 'is_work' => true]);
        $quiz->update(['title' => 'New Title', 'is_work' => false]);

        $activity = Activity::forSubject($quiz)->where('event', 'updated')->first();

        $this->assertNotNull($activity);
        $this->assertArrayHasKey('attributes', $activity->attribute_changes);
        $this->assertArrayHasKey('old', $activity->attribute_changes);
        $this->assertEquals(['title' => 'New Title', 'is_work' => false, 'for_telegram' => false, 'ignore_error' => false], $activity->attribute_changes['attributes']);
        $this->assertEquals(['title' => 'Old Title', 'is_work' => true, 'for_telegram' => false, 'ignore_error' => false], $activity->attribute_changes['old']);
    }

    public function test_logs_quiz_deletion(): void
    {
        $quiz = Quiz::factory()->create();
        $quiz->delete();

        $activity = Activity::forSubject($quiz)->where('event', 'deleted')->first();

        $this->assertNotNull($activity);
    }

    public function test_logs_activity_when_creating_quiz(): void
    {
        $tags = Tag::factory()->count(3)->create();

        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $data = [
            'title' => 'Sample Quiz',
            'is_work' => true,
            'timer_count' => 10,
            'fifty_fifty_hint' => true,
            'can_skip' => false,
            'for_telegram' => false,
            'ignore_error' => false,
            'image' => null,
            'description' => null,
        ];
        $response = $this->actingAs($user)->post(route('quiz.store'), $data + ['tags' => $tags->toArray()]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $quiz = Quiz::query()->where('title', 'Sample Quiz')->first();
        $this->assertNotNull($quiz);

        $activities = Activity::forSubject($quiz)->orderBy('id', 'desc')->get();
        $this->assertCount(
            2,
            $activities
        );
        $this->assertEquals(
            $data,
            $activities->where('event', 'created')->first()->attribute_changes['attributes']
        );
        $this->assertEquals(
            $tags->map->only('name')->toArray(),
            $activities->where('event', 'tags_updated')->first()->properties['tags']['attached']
        );
    }

    public function test_logs_activity_when_updating_quiz(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $quiz = Quiz::factory()->create();
        $response = $this->actingAs($user)->post(route('quiz.update', $quiz->id), [
            'title' => 'Updated Quiz',
            'is_work' => $quiz->is_work,
            'timer_count' => $quiz->timer_count,
            'fifty_fifty_hint' => $quiz->fifty_fifty_hint,
            'can_skip' => $quiz->can_skip,
            'for_telegram' => false,
            'ignore_error' => false,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $activities = Activity::forSubject($quiz)->orderBy('id', 'desc')->get();
        $this->assertCount(2, $activities);
        $this->assertEquals(['title' => 'Updated Quiz'], $activities->where('event', 'updated')->first()->attribute_changes['attributes']);
    }

    public function test_logs_activity_when_creating_question(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $data = [
            'question' => 'Sample Question',
            'image' => null,
            'audio' => null,
            'is_ai' => false,
        ];
        $answers = [
            ['id' => '1', 'text' => 'Answer 1', 'image' => null, 'is_correct' => false],
            ['id' => '2', 'text' => 'Answer 2', 'image' => null, 'is_correct' => true],
            ['id' => '3', 'text' => 'Answer 3', 'image' => null, 'is_correct' => false],
            ['id' => '4', 'text' => 'Answer 4', 'image' => null, 'is_correct' => false],
        ];
        $response = $this->actingAs($user)->post(route('question.store'), $data + ['answers' => $answers]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $question = Question::query()->where('question', 'Sample Question')->first();
        $this->assertNotNull($question);

        $activities = Activity::forSubject($question)->orderBy('id', 'desc')->get();
        $this->assertCount(
            2,
            $activities
        );
        $this->assertEquals(
            $data,
            $activities->where('event', 'created')->first()->attribute_changes['attributes']
        );
        $this->assertEquals(
            $answers,
            $activities->where('event', 'answers_updated')->first()->properties['answers']['attached']
        );
    }

    public function test_logs_activity_when_updating_question(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $question = Question::factory()->has(Answer::factory()->count(4))->create();
        $quizzes = Quiz::factory()->count(3)->create();
        $response = $this->actingAs($user)->post(route('question.update', $question->id), [
            'question' => 'Updated Question',
            'answers' => $question->answers->toArray(),
            'is_ai' => false,
            'quizzes' => $quizzes->pluck('id')->toArray(),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $activities = Activity::forSubject($question)->orderBy('id', 'desc')->get();
        $this->assertCount(3, $activities);
        $this->assertEquals(
            ['question' => 'Updated Question'],
            $activities->where('event', 'updated')->first()->attribute_changes['attributes']
        );
        $this->assertEquals(
            $quizzes->values()->map->only('id', 'title')->toArray(),
            $activities->where('event', 'quizzes_updated')->first()->properties['quizzes']['attached']
        );
    }
}
