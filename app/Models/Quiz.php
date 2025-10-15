<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * @return Collection<Quiz, $this>
     */
    public function getWorking(array $with = []): Collection
    {
        return self::query()
            ->select('id', 'title', 'image')
            ->where('is_work', 1)
            ->with($with)
            ->get();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_work' => 'boolean',
        ];
    }
}
