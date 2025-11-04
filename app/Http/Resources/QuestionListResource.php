<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

final class QuestionListResource extends JsonResource
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
            'image' => $this->image ? config('app.url').Storage::url($this->image) : null,
            'audio' => $this->audio ? config('app.url').Storage::url($this->audio) : null,
            'quizzes_ids' => $this->quizzes->pluck('id')->toArray(),
            'quizzes' => $this->quizzes->select('id', 'title', 'is_work'),
            'answers' => $this->answers->map(fn ($answer): array => [
                'id' => $answer->id,
                'text' => $answer->text,
                'image' => $answer->image ? config('app.url').Storage::url($answer->image) : null,
                'is_correct' => $answer->is_correct,
            ])->toArray(),
        ];
    }
}
