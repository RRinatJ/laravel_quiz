<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory, HasUuids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'current_question_id',
        'quiz_id',
        'correct_count',
        'question_row',
    ];

    /**
     * @return BelongsTo<Quiz, $this>
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * @return BelongsTo<Question, $this>
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'current_question_id');
    }

    /**
     * @return HasMany<GameStep, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(GameStep::class);
    }

    /**
     * @return HasOne<GameStep, $this>
     */
    public function latestStep(): HasOne
    {
        return $this->steps()->one()->latestOfMany();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'question_row' => 'array',
        ];
    }
}
