<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasLikes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Quiz extends Model
{
    /** @use HasFactory<\Database\Factories\QuizFactory> */
    use HasFactory, HasLikes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'is_work',
        'timer_count',
        'image',
        'fifty_fifty_hint',
        'can_skip',
        'for_telegram',
        'ignore_error',
        'description',
    ];

    /**
     * @return Collection<Quiz, $this>
     */
    public function getWorking(array $with = [], bool $has_questions = false): Collection
    {
        $query = self::query()
            ->select('id', 'title', 'description', 'image')
            ->where('is_work', 1);
        if ($has_questions) {
            $query->whereHas('questions');
        }

        return $query
            ->with($with)
            ->get();
    }

    /**
     * @return BelongsToMany<Question, $this>
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class);
    }

    /**
     * @return HasMany<Game, $this>
     */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_work' => 'boolean',
            'fifty_fifty_hint' => 'boolean',
            'can_skip' => 'boolean',
            'for_telegram' => 'boolean',
            'ignore_error' => 'boolean',
        ];
    }
}
