<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Quiz extends Model
{
    /** @use HasFactory<\Database\Factories\QuizFactory> */
    use HasFactory;

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
    ];

    /**
     * @return Collection<Quiz, $this>
     */
    public function getWorking(array $with = [], bool $has_questions = false): Collection
    {
        $query = self::query()
            ->select('id', 'title', 'image')
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
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_work' => 'boolean',
            'fifty_fifty_hint' => 'boolean',
            'can_skip' => 'boolean',
            'for_telegram' => 'boolean',
        ];
    }
}
