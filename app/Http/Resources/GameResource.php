<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class GameResource extends JsonResource
{
    public static $wrap;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $time_taken = (int) $this->created_at->diffInSeconds($this->updated_at);

        return [
            'id' => $this->id,
            'correct_count' => $this->correct_count,
            'quiz' => $this->quiz->only(['id', 'title', 'fifty_fifty_hint', 'can_skip']),
            'question' => $this->question->only(['id', 'question', 'image', 'audio', 'is_ai']),
            'fifty_fifty_hint' => $this->fifty_fifty_hint,
            'can_skip' => $this->can_skip,
            'time_taken' => gmdate('i:s', $time_taken),
        ];
    }
}
