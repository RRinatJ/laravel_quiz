<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Enums\UserRole;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Tag::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('tag.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Tag/TagList')
            ->has('tags.data', 3)
            ->has('message')
            ->has('error'));
    }

    public function test_tag_create(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($user)->get(route('tag.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Tag/TagForm'));
    }

    public function test_tag_store_validation_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($user)->post(route('tag.store'), [
            'name' => 'a', // Too short, should fail validation
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
    }

    public function test_tag_store_success(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($user)->post(route('tag.store'), [
            'name' => 'Test Tag',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('message', 'Tag Created Successfully');
        // Redirects to tag.index
        $response->assertRedirect(route('tag.index'));

        // Verify tag exists in database (spatie/laravel-tags stores name as JSON)
        $this->assertDatabaseHas('tags', [
            'name->en' => 'Test Tag',
        ]);
    }

    public function test_tag_edit(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        /** @var Tag $tag */
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->get(route('tag.edit', $tag->id));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Tag/TagForm')
            ->has('tag')
            ->has('message'));
    }

    public function test_tag_update_validation_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        /** @var Tag $tag */
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->put(route('tag.update', $tag->id), [
            'name' => 'a', // Too short
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name']);
        $response->assertRedirect();
    }

    public function test_tag_update_success(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        /** @var Tag $tag */
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->put(route('tag.update', $tag->id), [
            'name' => 'Updated Tag Name',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name->en' => 'Updated Tag Name',
        ]);
    }

    public function test_tag_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        /** @var Tag $tag */
        $tag = Tag::factory()->create();

        $response = $this->actingAs($user)->delete(route('tag.destroy', $tag->id));

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        // Redirects to home or tag.index
        $response->assertRedirect();
        // Verify tag was deleted from tags table
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_tag_search(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Tag::factory()->create(['name' => ['en' => 'PHP Basics', 'ru' => 'Основы PHP']]);
        Tag::factory()->create(['name' => ['en' => 'Laravel Guide', 'ru' => 'Руководство Laravel']]);

        $response = $this->actingAs($user)->get(route('tag.search', [
            'tag_name' => 'PHP',
        ]));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'PHP Basics']);
    }

    public function test_tag_search_validation_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($user)->get(route('tag.search', [
            'tag_name' => '',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['tag_name']);
    }

    public function test_tag_routes_require_authentication(): void
    {
        $tag = Tag::factory()->create();

        // GET routes should redirect to login when not authenticated
        $this->get(route('tag.index'))->assertRedirect(route('login'));
        $this->get(route('tag.create'))->assertRedirect(route('login'));
        $this->get(route('tag.edit', $tag->id))->assertRedirect(route('login'));
        $this->get(route('tag.search', ['tag_name' => 'test']))->assertRedirect(route('login'));

        // POST/PUT/DELETE routes should redirect to login when not authenticated
        $this->post(route('tag.store'), ['name' => 'Test'])->assertRedirect(route('login'));
        $this->put(route('tag.update', $tag->id), ['name' => 'Test'])->assertRedirect(route('login'));
        $this->delete(route('tag.destroy', $tag->id))->assertRedirect(route('login'));
    }

    public function test_tag_routes_require_admin_role(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::PLAYER]);
        $tag = Tag::factory()->create();

        // GET routes with abort_if should return 403
        $this->actingAs($user)->get(route('tag.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('tag.create'))->assertStatus(403);
        $this->actingAs($user)->get(route('tag.edit', $tag->id))->assertStatus(403);
        $this->actingAs($user)->get(route('tag.search', ['tag_name' => 'test']))->assertStatus(403);

        // POST/PUT/DELETE routes with FormRequest authorize() return 403
        $this->actingAs($user)->post(route('tag.store'), ['name' => 'Test'])->assertStatus(403);
        $this->actingAs($user)->put(route('tag.update', $tag->id), ['name' => 'Test'])->assertStatus(403);
        $this->actingAs($user)->delete(route('tag.destroy', $tag->id))->assertStatus(403);
    }
}
