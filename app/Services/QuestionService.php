<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Services\Tmdb\Service as TmdbService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

final class QuestionService
{
    public function store(array $data, ?UploadedFile $uplodedImage, ?UploadedFile $uplodedAudio, array $answerImages): Question
    {

        $tmdb_image = $data['tmdb_image'] ?? null;
        $quizzes_ids = $data['quizzes'] ?? [];
        $answers = $this->decodeAnswers($data['answers'] ?? []);
        $questionValidated = $this->sanitizeRequest($data);

        $createQuestion = Question::query()->create($questionValidated);
        $createQuestion->quizzes()->sync($quizzes_ids);

        if (is_null($uplodedImage) === false) {
            $createQuestion->image = $this->uploadFile($uplodedImage);
            $createQuestion->save();
        }

        if (is_null($tmdb_image) === false) {
            $tmdbService = new TmdbService();
            $createQuestion->image = $tmdbService->saveTmdbImage($tmdb_image);
            $createQuestion->save();
            if ($createQuestion->image !== '') {
                $createQuestion->tmdb_image()->create([
                    'tmdb_image' => $tmdb_image,
                ]);
            }
        }
        if (is_null($uplodedAudio) === false) {
            $createQuestion->audio = $this->uploadFile($uplodedAudio, 'audio');
            $createQuestion->save();
        }

        $this->processAnswers($createQuestion, $answers, $answerImages);

        return $createQuestion;
    }

    public function update(Question $question, array $data, ?UploadedFile $uplodedImage, ?UploadedFile $uplodedAudio, array $answerImages): Question
    {
        $tmdb_image = $data['tmdb_image'] ?? null;
        $quizzes_ids = $data['quizzes'] ?? [];
        $answers = $this->decodeAnswers($data['answers'] ?? []);

        $questionValidated = $this->sanitizeRequest($data);
        $updateQuestion = $question->update($questionValidated);
        if ($question->wasChanged('image')) {
            $question->tmdb_image()->delete();
        }

        if ($updateQuestion) {
            if (is_null($uplodedImage) === false) {
                $question->image = $this->uploadFile($uplodedImage);
                $question->save();
                $question->tmdb_image()->delete();
            }
            if (is_null($tmdb_image) === false) {
                $tmdbService = new TmdbService();
                $question->image = $tmdbService->saveTmdbImage($tmdb_image);
                $question->save();
                if ($question->image !== '') {
                    $question->tmdb_image()->updateOrCreate(
                        ['question_id' => $question->id],
                        ['tmdb_image' => $tmdb_image],
                    );
                }
            }
            if (is_null($uplodedAudio) === false) {
                $question->audio = $this->uploadFile($uplodedAudio, 'audio');
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
                $arr['image'] = $item->image ? config('app.url').Storage::url($item->image) : null;

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
