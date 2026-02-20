<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasLikes
{
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function likedByUsers(): MorphMany
    {
        return $this->likes()->with('user');
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function like(User $user): Like
    {
        return $this->likes()->create(['user_id' => $user->id]);
    }

    public function unlike(User $user): int
    {
        return $this->likes()->where('user_id', $user->id)->delete();
    }

    public function toggleLike(User $user): array
    {
        if ($this->isLikedBy($user)) {
            $this->unlike($user);

            return ['liked' => false, 'count' => $this->likesCount()];
        }

        $this->like($user);

        return ['liked' => true, 'count' => $this->likesCount()];
    }

    public function likesCount(): int
    {
        return $this->likes()->count();
    }
}
