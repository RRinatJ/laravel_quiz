<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreArticleRequest;
use App\Models\Article;
use App\Models\User;
use Exception;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

final class ArticleController extends Controller
{
    public function __construct(#[CurrentUser]
        private readonly User $user) {}

    public function index()
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        return Inertia::render('Article/ArticleList', [
            'articles' => Article::query()
                ->select('id', 'title', 'image', 'slug', 'created_at')
                ->paginate()
                ->withQueryString(),
            'message' => session('message'),
            'error' => session('error'),
        ]);
    }

    public function create()
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        return Inertia::render('Article/ArticleForm');
    }

    public function store(StoreArticleRequest $request)
    {
        /** @var Request $request */
        $uploaded_image = $request->hasFile('uploaded_image') ? $request->file('uploaded_image') : null;

        $articleValidated = $request->validated();
        unset($articleValidated['uploaded_image']);
        $articleValidated['slug'] = Str::slug($articleValidated['title'], '-');

        $createArticle = Article::query()->create($articleValidated);

        if ($createArticle) {
            if (is_null($uploaded_image) === false) {
                $createArticle->image = $uploaded_image->store('images', 'public');
                $createArticle->save();
            }

            return to_route('article.index')->with(['message' => 'Article Created Successfully']);
        }

        return abort(500);
    }

    public function edit(Article $article)
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        return Inertia::render('Article/ArticleForm', [
            'article' => $article,
            'message' => session('message'),
        ]);
    }

    public function update(StoreArticleRequest $request, Article $article)
    {
        /** @var Request $request */
        $uploaded_image = $request->hasFile('uploaded_image') ? $request->file('uploaded_image') : null;

        $articleValidated = $request->validated();
        unset($articleValidated['uploaded_image']);

        $articleValidated['slug'] = Str::slug($articleValidated['title'], '-');

        $updateArticle = $article->update($articleValidated);
        if ($updateArticle) {
            if (is_null($uploaded_image) === false) {
                $article->image = $uploaded_image->store('images', 'public');
                $article->save();
            }

            return redirect()->route('article.edit', $article->id)->with(['message' => 'Article Updated Successfully']);
        }

        return abort(500);
    }

    public function destroy(Article $article)
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        try {
            if ($article->delete() && $article->image) {
                Storage::disk('public')->delete($article->image);
            }

            return redirect()->route('article.index')->with('message', 'Article Deleted Successfully');
        } catch (Exception) {
            return redirect()->route('article.index')->with('error', 'Failed to delete the article');
        }
    }

    public function by_slug(Article $article)
    {
        return Inertia::render('Article/ArticlePage', [
            'article' => $article->only('id', 'title', 'content', 'image', 'created_at'),
        ]);
    }
}
