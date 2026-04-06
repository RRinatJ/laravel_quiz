<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\Tag as SpatieTag;

final class Tag extends SpatieTag
{
    public function quizzes(): MorphToMany
    {
        return $this->morphedByMany(Quiz::class, 'taggable');
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value): string => date('Y-m-d H:i:s', strtotime($value)),
        );
    }
}
