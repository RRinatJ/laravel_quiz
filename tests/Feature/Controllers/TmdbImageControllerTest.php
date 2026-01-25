<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Contracts\Tmdb\ApiSearch;
use App\Contracts\Tmdb\FactoryServiceInterface;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

final class TmdbImageControllerTest extends TestCase
{
    use RefreshDatabase;

    private ApiSearch $apiSearchMock;

    private FactoryServiceInterface $factoryServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiSearchMock = Mockery::mock(ApiSearch::class);
        $this->factoryServiceMock = Mockery::mock(FactoryServiceInterface::class);

        $this->app->instance(FactoryServiceInterface::class, $this->factoryServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function test_search_endpoint_returns_successful_response_with_valid_parameters(): void
    {
        $type = 'movie';
        $query = 'inception';
        $mockResponse = [
            'page' => 1,
            'results' => [
                0 => [
                    'id' => 27205,
                    'original_title' => 'Inception',
                    'overview' => 'Description',
                    'poster_path' => '/xlaY2zyzMfkhk0HSC5VUwzoZPU1.jpg',
                    'release_date' => '2010-07-15',
                    'title' => 'Inception',
                ],
                1 => [
                    'id' => 613092,
                    'original_title' => 'El crack cero',
                    'overview' => 'Description 2',
                    'poster_path' => '/kzgPu2CMxBr4YZZxC1Off4cUfR9.jpg',
                    'release_date' => '2019-10-04',
                    'title' => 'The Crack: Inception',
                ],
            ],
            'total_pages' => 1,
            'total_results' => 2,
        ];
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->factoryServiceMock
            ->shouldReceive('init')
            ->with($type)
            ->once()
            ->andReturn($this->apiSearchMock);

        $this->apiSearchMock
            ->shouldReceive('search')
            ->with($query)
            ->once()
            ->andReturn($mockResponse);

        $response = $this->actingAs($user)
            ->getJson(route('tmdb.search', ['type' => $type, 'query' => $query]));

        $response->assertStatus(200)
            ->assertJson($mockResponse);
    }

    public function test_images_endpoint_returns_successful_response_with_valid_parameters(): void
    {
        $type = 'movie';
        $tmdb_id = 27205;
        $mockResponse = [
            'id' => 27205,
            'backdrops' => [],
            'posters' => [],
        ];
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $this->factoryServiceMock
            ->shouldReceive('init')
            ->with($type)
            ->once()
            ->andReturn($this->apiSearchMock);

        $this->apiSearchMock
            ->shouldReceive('images')
            ->with($tmdb_id)
            ->once()
            ->andReturn($mockResponse);

        $response = $this->actingAs($user)
            ->getJson(route('tmdb.images', ['type' => $type, 'tmdb_id' => $tmdb_id]));

        $response->assertStatus(200)
            ->assertJson($mockResponse);
    }

    public function test_search_validation_error(): void
    {
        $type = 'invalid_type';
        $query = 'inception';
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($user)
            ->get(route('tmdb.search', ['type' => $type, 'query' => $query]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['type']);
    }

    public function test_images_validation_error(): void
    {
        $type = 'invalid_type';
        $tmdb_id = 27205;
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($user)
            ->get(route('tmdb.images', ['type' => $type, 'tmdb_id' => $tmdb_id]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['type']);
    }

    public function test_unauthenticated_user_cannot_access_endpoints(): void
    {
        $responseSearch = $this->getJson(route('tmdb.search', ['type' => 'movie', 'query' => 'inception']));
        $responseImages = $this->getJson(route('tmdb.images', ['type' => 'movie', 'tmdb_id' => 27205]));

        $responseSearch->assertStatus(Response::HTTP_UNAUTHORIZED);
        $responseImages->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_routes_require_admin_role(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::PLAYER]);

        $responseSearch = $this->actingAs($user)
            ->getJson(route('tmdb.search', ['type' => 'movie', 'query' => 'inception']));
        $responseImages = $this->actingAs($user)
            ->getJson(route('tmdb.images', ['type' => 'movie', 'tmdb_id' => 27205]));

        $responseSearch->assertStatus(Response::HTTP_FORBIDDEN);
        $responseImages->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
