<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        Article::factory()->count(3)->create();
        $response = $this->actingAs($user)->get(route('article.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Article/ArticleList')
            ->has('articles.data', 3)
            ->has('message')
            ->has('error'));
    }

    public function test_article_create(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $response = $this->actingAs($user)->get(route('article.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Article/ArticleForm'));
    }

    public function test_article_store_validation_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $response = $this->actingAs($user)->post(route('article.store'), [
            'title' => '',
            'content' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['title', 'content']);
    }

    public function test_article_store_success(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $response = $this->actingAs($user)->post(route('article.store'), [
            'title' => 'Sample Article',
            'content' => 'This is a sample article content.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('article.index'));
        $this->assertDatabaseHas('articles', [
            'title' => 'Sample Article',
            'content' => 'This is a sample article content.',
        ]);
    }

    public function test_article_edit(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        /** @var Article $article */
        $article = Article::factory()->create();

        $response = $this->actingAs($user)->get(route('article.edit', $article->id));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Article/ArticleForm')
            ->has('article')
            ->has('message'));
    }

    public function test_article_update_validation_error(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        /** @var Article $article */
        $article = Article::factory()->create();

        $response = $this->actingAs($user)->post(route('article.update', $article->id), [
            'title' => '',
            'content' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['title', 'content']);
    }

    public function test_article_update_success(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        /** @var Article $article */
        $article = Article::factory()->create();

        $response = $this->actingAs($user)->post(route('article.update', $article->id), [
            'title' => 'Updated Article Title',
            'content' => 'Updated content for the article.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('article.edit', $article->id));
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'Updated Article Title',
            'content' => 'Updated content for the article.',
        ]);
    }

    public function test_article_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        /** @var Article $article */
        $article = Article::factory()->create();

        $response = $this->actingAs($user)->delete(route('article.destroy', $article->id));

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('article.index'));
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_article_by_slug(): void
    {
        /** @var Article $article */
        $article = Article::factory()->create();

        $response = $this->get(route('article.slug', $article->slug));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page): Assert => $page
            ->component('Article/ArticlePage')
            ->has('article', fn (Assert $page): Assert => $page
                ->where('id', $article->id)
                ->where('title', $article->title)
                ->where('content', $article->content)
                ->where('image', $article->image)
                ->etc()
            ));
    }

    public function test_article_by_slug_not_found(): void
    {
        $response = $this->get(route('article.slug', 'non-existing-slug'));

        $response->assertStatus(404);
    }

    public function test_article_routes_require_authentication(): void
    {
        $article = Article::factory()->create();

        $this->get(route('article.index'))->assertRedirect(route('login'));
        $this->get(route('article.create'))->assertRedirect(route('login'));
        $this->post(route('article.store'), [])->assertRedirect(route('login'));
        $this->get(route('article.edit', $article->id))->assertRedirect(route('login'));
        $this->post(route('article.update', $article->id), [])->assertRedirect(route('login'));
        $this->delete(route('article.destroy', $article->id))->assertRedirect(route('login'));
    }

    public function test_article_routes_require_admin_role(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => UserRole::PLAYER]);
        $article = Article::factory()->create();

        $this->actingAs($user)->get(route('article.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('article.create'))->assertStatus(403);
        $this->actingAs($user)->post(route('article.store'), [])->assertStatus(403);
        $this->actingAs($user)->get(route('article.edit', $article->id))->assertStatus(403);
        $this->actingAs($user)->post(route('article.update', $article->id), [])->assertStatus(403);
        $this->actingAs($user)->delete(route('article.destroy', $article->id))->assertStatus(403);
    }
}
