<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'question',
        'image',
        'audio',
        'is_ai',
    ];

    /**
     * @return BelongsToMany<Quiz, $this>
     */
    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(Quiz::class)->withTimestamps();
    }

    /**
     * @return HasMany<Answer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * @return HasOne<TmdbImage, $this>
     */
    public function tmdb_image(): HasOne
    {
        return $this->HasOne(TmdbImage::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_ai' => 'boolean',
        ];
    }
}
