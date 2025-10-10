<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;
use Illuminate\Http\UploadedFile;

final readonly class QuizUpdateAction
{
    public function handle(Quiz $quiz, array $data, ?UploadedFile $uploaded_image): Quiz
    {
        unset($data['uploaded_image']);
        $updateQuiz = $quiz->update($data);
        if ($updateQuiz && is_null($uploaded_image) === false) {
            $quiz->image = $uploaded_image->store('images', 'public');
            $quiz->save();
        }

        return $quiz;
    }
}
