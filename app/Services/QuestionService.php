<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Services\Tmdb\Service as TmdbService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

final class QuestionService
{
    public function store(array $data, ?UploadedFile $uplodedImage, ?UploadedFile $uplodedAudio, array $answerImages): Question
    {

        $tmdb_image = empty($data['tmdb_image']) ? null : $data['tmdb_image'];
        $quizzes_ids = $data['quizzes'] ?? [];
        $answers = $this->decodeAnswers($data['answers'] ?? []);
        $questionValidated = $this->sanitizeRequest($data);

        $createQuestion = Question::query()->create($questionValidated);
        $quiz_changes = $createQuestion->quizzes()->sync($quizzes_ids);
        $this->logQuizzesChanges($createQuestion, $quiz_changes);

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

        $answer_changes = $this->processAnswers($createQuestion, $answers, $answerImages);
        $this->logAnswersChanges($createQuestion, [], $answer_changes);

        return $createQuestion;
    }

    public function update(Question $question, array $data, ?UploadedFile $uplodedImage, ?UploadedFile $uplodedAudio, array $answerImages): Question
    {
        $tmdb_image = empty($data['tmdb_image']) ? null : $data['tmdb_image'];
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

            $quiz_changes = $question->quizzes()->sync($quizzes_ids);
            $this->logQuizzesChanges($question, $quiz_changes);

            $init_answers = $question->answers->keyBy('id')->toArray();
            $answer_ids = $question->answers->pluck('id')->toArray();
            $answer_changes = $this->processAnswers($question, $answers, $answerImages, $answer_ids);
            $this->logAnswersChanges($question, $init_answers, $answer_changes);
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

    public function logQuizzesChanges(Question $question, array $changes): void
    {
        if (! empty($changes['attached']) || ! empty($changes['detached'])) {
            $quizzes = [];
            $db_quizzes = Quiz::query()
                ->whereIn('id', array_merge($changes['attached'], $changes['detached']))
                ->select('id', 'title')
                ->get();

            $quizzes['attached'] = $db_quizzes->whereIn('id', $changes['attached'])->values()->toArray();
            $quizzes['detached'] = $db_quizzes->whereIn('id', $changes['detached'])->values()->toArray();

            activity()
                ->causedBy(Auth::user())
                ->performedOn($question)
                ->event('quizzes_updated')
                ->withProperties([
                    'quizzes' => $quizzes,
                ])
                ->log('Updated quizzes');
        }
    }

    public function logAnswersChanges(Question $question, array $init_answers, array $changes): void
    {
        if (! empty($changes['attached']) || ! empty($changes['detached']) || ! empty($changes['updated'])) {
            $answers = [
                'detached' => [],
                'updated' => [],
            ];
            if (! empty($changes['updated'])) {
                foreach ($changes['updated'] as $updated) {
                    $db_answer = $init_answers[$updated['id']] ?? [];
                    if ($db_answer) {
                        $temp = [];
                        foreach ($updated as $field => $value) {
                            if ($db_answer[$field] !== $value) {
                                $temp['id'] = $updated['id'];
                                $temp['attributes'][$field] = $value;
                                $temp['old'][$field] = $db_answer[$field];
                            }
                        }
                        $answers['updated'][] = $temp;
                    }
                }
            }
            if (! empty($changes['detached'])) {
                foreach ($changes['detached'] as $detached) {
                    if (isset($init_answers[$detached])) {
                        $answers['detached'][] = Arr::only($init_answers[$detached], ['id', 'text', 'image', 'is_correct']);
                    }
                }
            }
            activity()
                ->causedBy(Auth::user())
                ->performedOn($question)
                ->event('answers_updated')
                ->withProperties([
                    'answers' => [
                        'attached' => $changes['attached'],
                        'detached' => $answers['detached'],
                        'updated' => $answers['updated'],
                    ],
                ])
                ->log('Updated answers');
        }
    }

    private function processAnswers(Question $question, array $answers, array $answerImages, array $answer_ids = []): array
    {
        $createdAnswers = [];
        $data_answer_ids = [];
        $tmdb_image_add = [];
        $tmdbService = new TmdbService();
        $changes = [
            'attached' => [],
            'detached' => [],
            'updated' => [],
        ];

        foreach ($answers as $answer) {
            $answer['image'] = empty($answer['image']) ? null : $this->findAndUploadAnswerImage($answer['image'], $answerImages);

            $tmdb_image = empty($answer['tmdb_image']) ? null : $answer['tmdb_image'];
            if (is_null($tmdb_image) === false) {
                $answer['image'] = $tmdbService->saveTmdbImage($tmdb_image);
            }

            $updated_data = [
                'text' => mb_trim((string) $answer['text']),
                'image' => $answer['image'],
                'is_correct' => $answer['is_correct'],
            ];

            if (in_array($answer['id'], $answer_ids)) {
                $data_answer_ids[] = $answer['id'];
                $update_answer = $question->answers->firstWhere('id', $answer['id']);
                $update_answer->update($updated_data);
                if ($update_answer->wasChanged()) {
                    $updated_data['id'] = $update_answer->id;
                    $changes['updated'][] = $updated_data;
                }
                if ($update_answer->wasChanged('image')) {
                    $update_answer->tmdb_image()->delete();
                }

                if (is_null($tmdb_image) === false) {
                    $update_answer->tmdb_image()->updateOrCreate(
                        ['answer_id' => $update_answer->id],
                        [
                            'tmdb_image' => $tmdb_image,
                        ],
                    );
                }

                continue;
            }

            $createdAnswers[] = new Answer($updated_data);

            if (is_null($tmdb_image) === false) {
                $tmdb_image_add[$updated_data['image']] = $tmdb_image;
            }
        }

        if ($createdAnswers !== []) {
            $saved_answers = $question->answers()->saveMany($createdAnswers);
            $changes['attached'] = collect($saved_answers)->map->only('id', 'text', 'image', 'is_correct')->toArray();

            foreach ($question->answers as $createdAnswer) {
                if (array_key_exists($createdAnswer->image, $tmdb_image_add)) {
                    $createdAnswer->tmdb_image()->create([
                        'tmdb_image' => $tmdb_image_add[$createdAnswer->image],
                    ]);
                }
            }
        }

        $deleted_ids = array_diff($answer_ids, $data_answer_ids);
        if ($deleted_ids !== []) {
            $changes['detached'] = $deleted_ids;
            Answer::query()->whereIn('id', $deleted_ids)->delete();
        }

        return $changes;
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
