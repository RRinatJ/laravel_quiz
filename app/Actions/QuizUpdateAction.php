<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

final readonly class QuizUpdateAction
{
    public function __construct(
        private TagLogAction $action) {}

    public function handle(Quiz $quiz, array $data, ?UploadedFile $uploaded_image): Quiz
    {
        $tags = $data['tags'] ?? [];
        unset($data['tags']);
        unset($data['uploaded_image']);
        $updateQuiz = $quiz->update($data);
        $tag_changes = $quiz->tags()->sync(Arr::pluck($tags, 'id'));
        $this->action->handle($quiz, $tag_changes);
        if ($updateQuiz && is_null($uploaded_image) === false) {
            $quiz->image = $uploaded_image->store('images', 'public');
            $quiz->save();
        }

        return $quiz;
    }
}
