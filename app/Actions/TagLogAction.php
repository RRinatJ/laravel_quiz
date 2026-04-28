<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

final class TagLogAction
{
    public function handle(Quiz $quiz, array $changes): void
    {
        if (! empty($changes['attached']) || ! empty($changes['detached'])) {
            $tags = [];
            $db_tags = Tag::query()
                ->whereIn('id', array_merge($changes['attached'], $changes['detached']))
                ->select('id', 'name')
                ->get();

            $tags['attached'] = $db_tags->whereIn('id', $changes['attached'])->values()->map(fn ($tag): array => [
                'name' => $tag->name,
            ])->toArray();
            $tags['detached'] = $db_tags->whereIn('id', $changes['detached'])->values()->map(fn ($tag): array => [
                'name' => $tag->name,
            ])->toArray();

            activity()
                ->causedBy(Auth::user())
                ->performedOn($quiz)
                ->event('tags_updated')
                ->withProperties([
                    'tags' => $tags,
                ])
                ->log('Updated tags');
        }
    }
}
