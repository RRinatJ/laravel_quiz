<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Resources\QuestionListResource;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use App\Services\QuestionService;
use Exception;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class QuestionController extends Controller
{
    public function __construct(#[CurrentUser]
        private readonly User $user) {}

    public function index(Request $request, Quiz $quiz_model): Response
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);
        $quiz_id = (int) ($request->input('quiz_id'));

        return Inertia::render('Question/QuestionList', [
            'questions' => QuestionListResource::collection(
                Question::query()
                    ->when($quiz_id, function ($query, $quiz_id): void {
                        $query->whereHas('quizzes', function (Builder $sub_query) use ($quiz_id): void {
                            $sub_query->where('quiz_id', $quiz_id);
                        });
                    })
                    ->select('id', 'question', 'image', 'audio', 'is_ai')
                    ->with('quizzes', 'answers')
                    ->paginate(5)
                    ->withQueryString()
            ),
            'message' => session('message'),
            'error' => session('error'),
            'filters' => $request->only(['quiz_id']),
            'quizzes' => $quiz_model->getWorking(),
        ]);
    }

    public function create(Quiz $quiz_model): Response
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        return Inertia::render('Question/QuestionForm', [
            'quizzes' => $quiz_model->select('id', 'title')->get(),
            'is_ai_available' => (bool) config('prism.providers.gemini.api_key'),
            'is_tmdb_available' => (bool) config('services.tmdb.api_key'),
        ]);
    }

    public function store(StoreQuestionRequest $request, QuestionService $questionService): RedirectResponse
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);
        try {
            /** @var Request $request */
            $uploaded_image = $request->hasFile('uploaded_image') ? $request->file('uploaded_image') : null;
            $uploaded_audio = $request->hasFile('uploaded_audio') ? $request->file('uploaded_audio') : null;

            $answer_images = $request->hasFile('answer_images') ? $request->file('answer_images') : [];

            $questionService->store(
                $request->validated(),
                $uploaded_image,
                $uploaded_audio,
                $answer_images
            );

            return to_route('question.index')->with(['message' => 'Question Created Successfully']);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }

    public function edit(Question $question, Quiz $quiz_model): Response
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        return Inertia::render('Question/QuestionForm', [
            'question' => new QuestionResource($question),
            'quizzes' => $quiz_model->select('id', 'title')->get(),
            'message' => session('message'),
            'is_ai_available' => (bool) config('prism.providers.gemini.api_key'),
            'is_tmdb_available' => (bool) config('services.tmdb.api_key'),
        ]);
    }

    public function update(Question $question, StoreQuestionRequest $request, QuestionService $questionService): RedirectResponse
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);
        try {
            /** @var Request $request */
            $uploaded_image = $request->hasFile('uploaded_image') ? $request->file('uploaded_image') : null;
            $uploaded_audio = $request->hasFile('uploaded_audio') ? $request->file('uploaded_audio') : null;

            $answer_images = $request->hasFile('answer_images') ? $request->file('answer_images') : [];

            $questionService->update(
                $question,
                $request->validated(),
                $uploaded_image,
                $uploaded_audio,
                $answer_images
            );

            return to_route('question.edit', $question->id)->with(['message' => 'Question Updated Successfully']);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }

    public function destroy(Question $question): RedirectResponse
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);
        try {
            $question->load('answers');
            if ($question->delete()) {
                if ($question->image) {
                    Storage::disk('public')->delete($question->image);
                }
                foreach ($question->answers as $answer) {
                    if ($answer->image) {
                        Storage::disk('public')->delete($answer->image);
                    }
                }
                $question->tmdb_image()->delete();
            }

            return redirect()->route('question.index')->with('message', 'Quiz Deleted Successfully');
        } catch (Exception $e) {
            logger($e->getMessage());

            return redirect()->route('question.index')->with('error', 'Failed to delete the quiz');
        }
    }
}
