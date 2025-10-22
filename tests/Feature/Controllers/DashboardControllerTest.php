<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Quiz::factory()->has(Question::factory()->count(4))->count(3)->create();
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Dashboard')
            ->has('latestQuestions', 5)
            ->has('latestQuizzes', 3)
        );
    }
}
