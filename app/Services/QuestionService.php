<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class QuestionService
{
    public function store(array $data, ?UploadedFile $uplodedImage, array $answerImages): Question
    {
        $quizzes_ids = $data['quizzes'] ?? [];
        $answers = $this->decodeAnswers($data['answers'] ?? []);
        $questionValidated = $this->sanitizeRequest($data);

        $createQuestion = Question::query()->create($questionValidated);
        $createQuestion->quizzes()->sync($quizzes_ids);

        if (is_null($uplodedImage) === false) {
            $createQuestion->image = $this->uploadFile($uplodedImage);
            $createQuestion->save();
        }

        $this->processAnswers($createQuestion, $answers, $answerImages);

        return $createQuestion;
    }

    public function update(Question $question, array $data, ?UploadedFile $uplodedImage, array $answerImages): Question
    {
        $quizzes_ids = $data['quizzes'] ?? [];
        $answers = $this->decodeAnswers($data['answers'] ?? []);

        $questionValidated = $this->sanitizeRequest($data);
        $updateQuestion = $question->update($questionValidated);

        if ($updateQuestion) {
            if (is_null($uplodedImage) === false) {
                $path = $this->uploadFile($uplodedImage, 'images');
                $question->image = $path;
                $question->save();
            }

            $question->quizzes()->sync($quizzes_ids);

            $answer_ids = $question->answers->pluck('id')->toArray();

            $this->processAnswers($question, $answers, $answerImages, $answer_ids);
        }

        return $question;
    }

    public function getLatestQuestions(?int $quiz_id = null, ?int $count = null): Collection
    {
        if (is_null($count)) {
            $count = 5;
        }

        return Question::query()
            ->when($quiz_id, function ($query, $quiz_id): void {
                $query->whereHas('quizzes', function (Builder $sub_query) use ($quiz_id): void {
                    $sub_query->where('quiz_id', $quiz_id);
                });
            })
            ->select('id', 'question', 'image', 'created_at')
            ->with('quizzes:id,title')
            ->latest()
            ->take($count)
            ->get()
            ->map(function ($item) {
                $arr = $item->toArray();
                $arr['created_at'] = $item->created_at->diffForHumans();

                return $arr;
            });
    }

    private function processAnswers(Question $question, array $answers, array $answerImages, array $answer_ids = []): void
    {
        $createdAnswers = [];
        $data_answer_ids = [];
        foreach ($answers as $answer) {
            $answer['image'] = empty($answer['image']) ? null : $this->findAndUploadAnswerImage($answer['image'], $answerImages);

            $updated_data = [
                'text' => mb_trim((string) $answer['text']),
                'image' => $answer['image'],
                'is_correct' => $answer['is_correct'],
            ];

            if (in_array($answer['id'], $answer_ids)) {
                $data_answer_ids[] = $answer['id'];
                $update_answer = $question->answers->firstWhere('id', $answer['id']);
                $update_answer->update($updated_data);

                continue;
            }

            $createdAnswers[] = new Answer($updated_data);
        }

        if ($createdAnswers !== []) {
            $question->answers()->saveMany($createdAnswers);
        }

        $deleted_ids = array_diff($answer_ids, $data_answer_ids);
        if ($deleted_ids !== []) {
            Answer::query()->whereIn('id', $deleted_ids)->delete();
        }
    }

    private function findAndUploadAnswerImage(string $imageName, array $answerImages): string
    {
        foreach ($answerImages as $image) {
            if ($image->getClientOriginalName() === $imageName) {
                return $this->uploadFile($image);
            }
        }

        return $imageName;
    }

    private function decodeAnswers(array|string $answers): array
    {
        return is_string($answers) ? json_decode($answers, true) : $answers;
    }

    private function sanitizeRequest(array $data): array
    {
        return Arr::only($data, (new Question)->getFillable());
    }

    private function uploadFile(UploadedFile $file, string $directory = 'images'): string
    {
        return $file->store($directory, 'public');
    }
}
