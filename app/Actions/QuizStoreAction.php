<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Quiz;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

final readonly class QuizStoreAction
{
    public function handle(array $data, ?UploadedFile $uploaded_image): Quiz
    {
        $tags = $data['tags'] ?? [];
        unset($data['tags']);
        unset($data['uploaded_image']);
        $createQuiz = Quiz::query()->create($data);
        $createQuiz->syncTags(Arr::pluck($tags, 'name'));
        if ($createQuiz && is_null($uploaded_image) === false) {
            $createQuiz->image = $uploaded_image->store('images', 'public');
            $createQuiz->save();
        }

        return $createQuiz;
    }
}
