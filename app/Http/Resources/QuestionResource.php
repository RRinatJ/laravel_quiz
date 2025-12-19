<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question' => $this->question,
            'image' => $this->image,
            'audio' => $this->audio,
            'quizzes_ids' => $this->quizzes->pluck('id')->toArray(),
            'answers' => $this->answers->map(fn ($answer): array => [
                'id' => $answer->id,
                'text' => $answer->text,
                'image' => $answer->image,
                'is_correct' => $answer->is_correct,
            ])->toArray(),
            'is_ai' => $this->is_ai,
        ];
    }
}
