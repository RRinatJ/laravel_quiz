<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Game;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
final class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'user_id' => User::factory(),
            'correct_count' => 0,
            'question_row' => [],
            'fifty_fifty_hint' => false,
            'can_skip' => false,
        ];
    }

    /**
     * Indicate that the game belongs to a specific quiz.
     */
    public function forQuiz(Quiz $quiz): static
    {
        return $this->state(fn (array $attributes): array => [
            'quiz_id' => $quiz->id,
        ]);
    }

    /**
     * Indicate that the game belongs to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }
}
