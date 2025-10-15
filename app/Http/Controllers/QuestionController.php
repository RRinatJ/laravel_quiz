<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuestionRequest;
use App\Http\Resources\QuestionListResource;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\Quiz;
use App\Services\QuestionService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class QuestionController extends Controller
{
    public function index(Request $request, Quiz $quiz_model): Response
    {
        $quiz_id = (int) ($request->input('quiz_id'));

        return Inertia::render('Question/QuestionList', [
            'questions' => QuestionListResource::collection(
                Question::query()
                    ->when($quiz_id, function ($query, $quiz_id): void {
                        $query->whereHas('quizzes', function (Builder $sub_query) use ($quiz_id): void {
                            $sub_query->where('quiz_id', $quiz_id);
                        });
                    })
                    ->select('id', 'question', 'image')
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
        return Inertia::render('Question/QuestionForm', [
            'quizzes' => $quiz_model->select('id', 'title')->get(),
        ]);
    }

    public function store(StoreQuestionRequest $request, QuestionService $questionService): RedirectResponse
    {
        try {
            /** @var Request $request */
            $uploaded_image = $request->hasFile('uploaded_image') ? $request->file('uploaded_image') : null;

            $answer_images = $request->hasFile('answer_images') ? $request->file('answer_images') : [];

            $questionService->store(
                $request->validated(),
                $uploaded_image,
                $answer_images
            );

            return to_route('question.index')->with(['message' => 'Question Created Successfully']);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }

    public function edit(Question $question, Quiz $quiz_model): Response
    {
        return Inertia::render('Question/QuestionForm', [
            'question' => new QuestionResource($question),
            'quizzes' => $quiz_model->select('id', 'title')->get(),
            'message' => session('message'),
        ]);
    }

    public function update(Question $question, StoreQuestionRequest $request, QuestionService $questionService): RedirectResponse
    {
        try {
            /** @var Request $request */
            $uploaded_image = $request->hasFile('uploaded_image') ? $request->file('uploaded_image') : null;

            $answer_images = $request->hasFile('answer_images') ? $request->file('answer_images') : [];

            $questionService->update(
                $question,
                $request->validated(),
                $uploaded_image,
                $answer_images
            );

            return to_route('question.edit', $question->id)->with(['message' => 'Question Updated Successfully']);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['errors' => $e->getMessage()]);
        }
    }

    public function destroy(Question $question): RedirectResponse
    {
        try {
            $question->delete();

            return redirect()->route('question.index')->with('message', 'Quiz Deleted Successfully');
        } catch (Exception $e) {
            logger($e->getMessage());

            return redirect()->route('question.index')->with('error', 'Failed to delete the quiz');
        }
    }
}
