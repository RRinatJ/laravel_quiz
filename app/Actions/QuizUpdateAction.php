<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

final readonly class QuizUpdateAction
{
    public function handle(Quiz $quiz, array $data, ?UploadedFile $uploaded_image): Quiz
    {
        $tags = $data['tags'] ?? [];
        unset($data['tags']);
        unset($data['uploaded_image']);
        $updateQuiz = $quiz->update($data);
        $quiz->syncTags(Arr::pluck($tags, 'name'));
        if ($updateQuiz && is_null($uploaded_image) === false) {
            $quiz->image = $uploaded_image->store('images', 'public');
            $quiz->save();
        }

        return $quiz;
    }
}
