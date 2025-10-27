<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\QuizStoreAction;
use App\Actions\QuizUpdateAction;
use App\Enums\UserRole;
use App\Http\Requests\StoreQuizRequest;
use App\Models\Quiz;
use App\Models\User;
use Exception;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class QuizController extends Controller
{
    public function __construct(#[CurrentUser]
        private readonly User $user) {}

    public function index(): Response
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        return Inertia::render('Quiz/QuizList', [
            'quizzes' => Quiz::query()
                ->select('id', 'title', 'is_work', 'image', 'timer_count')
                ->paginate(5)
                ->withQueryString(),
            'message' => session('message'),
            'error' => session('error'),
        ]);
    }

    public function create(): Response
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        return Inertia::render('Quiz/QuizForm');
    }

    public function store(StoreQuizRequest $request, QuizStoreAction $action): RedirectResponse
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);
        /** @var Request $request */
        $uploaded_image = $request->hasFile('uploaded_image') ? $request->file('uploaded_image') : null;
        $quizValidated = $request->validated();

        if ($action->handle($quizValidated, $uploaded_image)) {
            return to_route('quiz.index')->with(['message' => 'Quiz Created Successfully']);
        }

        return abort(500);
    }

    public function edit(Quiz $quiz): Response
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);

        return Inertia::render('Quiz/QuizForm', [
            'quiz' => $quiz,
            'message' => session('message'),
        ]);
    }

    public function update(StoreQuizRequest $request, Quiz $quiz, QuizUpdateAction $action): RedirectResponse
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);
        /** @var Request $request */
        $uploaded_image = $request->hasFile('uploaded_image') ? $request->file('uploaded_image') : null;
        $quizValidated = $request->validated();

        if ($action->handle($quiz, $quizValidated, $uploaded_image)) {
            return redirect()->route('quiz.edit', $quiz->id)->with(['message' => 'Quiz Updated Successfully']);
        }

        return abort(500);
    }

    public function destroy(Quiz $quiz): RedirectResponse
    {
        abort_if(! $this->user->checkRole(UserRole::ADMIN), 403);
        try {
            if ($quiz->delete() && $quiz->image) {
                Storage::disk('public')->delete($quiz->image);
            }

            return redirect()->route('quiz.index')->with('message', 'Quiz Deleted Successfully');
        } catch (Exception) {
            return redirect()->route('quiz.index')->with('error', 'Failed to delete the quiz');
        }
    }
}
