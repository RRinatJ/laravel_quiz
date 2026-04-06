<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\SearchTagRequest;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagListResource;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

final class TagController extends Controller
{
    public function __construct(#[CurrentUser]
        private readonly User $user) {}

    public function index()
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        return Inertia::render('Tag/TagList', [
            'tags' => TagListResource::collection(
                Tag::query()
                    ->orderBy('order_column')
                    ->paginate()
                    ->withQueryString()
            ),
            'message' => session('message'),
            'error' => session('error'),
        ]);
    }

    public function create()
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        return Inertia::render('Tag/TagForm');
    }

    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();

        $tag = Tag::query()->create([
            'name' => $validated['name'],
        ]);

        if ($tag) {
            return to_route('tag.index')->with(['message' => 'Tag Created Successfully']);
        }

        return abort(500);
    }

    public function edit(Tag $tag)
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        return Inertia::render('Tag/TagForm', [
            'tag' => new TagResource($tag),
            'message' => session('message'),
        ]);
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $validated = $request->validated();

        $tag->setTranslation('name', app()->getLocale(), $validated['name']);
        $tag->save();

        return redirect()->route('tag.edit', $tag->id)->with(['message' => 'Tag Updated Successfully']);
    }

    public function destroy(Tag $tag)
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        try {
            if ($tag->delete()) {
                return redirect()->route('tag.index')->with('message', 'Tag Deleted Successfully');
            }

            return abort(500);
        } catch (Exception) {
            return redirect()->route('tag.index')->with('error', 'Failed to delete the tag');
        }
    }

    public function search(SearchTagRequest $request): JsonResponse
    {
        $tag_name = $request->string('tag_name');
        $tags = TagListResource::collection(Tag::containing($tag_name)->get());

        return response()->json($tags);
    }
}
