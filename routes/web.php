<?php

declare(strict_types=1);

use App\Http\Controllers\AiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReportController;
use App\Models\Quiz;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::get('/', fn (Quiz $quiz_model): Response => Inertia::render('SelectQuiz', [
    'quizzes' => $quiz_model->getWorking([], true),
]))->name('home');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/game/{quiz_id}', [GameController::class, 'store'])->name('game.create');
Route::get('/game/questions/{game_id}', [GameController::class, 'show'])->name('game.show');
Route::post('/game/{game_id}', [GameController::class, 'edit'])->name('game.edit');
Route::get('/game/set_update/{game_id}', [GameController::class, 'setUpdate'])->name('game.set_update');

Route::middleware('auth')->group(function (): void {
    Route::resource('quiz', QuizController::class)->except([
        'update', 'show',
    ]);
    Route::post('/quiz/{quiz}', [QuizController::class, 'update'])->name('quiz.update');

    Route::resource('question', QuestionController::class)->except([
        'update', 'show',
    ]);
    Route::post('/question/{question}', [QuestionController::class, 'update'])->name('question.update');

    Route::get('/ai/get_question', [AiController::class, 'get_question'])->name('ai.get_question');

    Route::get('/reports/popular-quizzes', [ReportController::class, 'popularQuizzes'])->name('reports.popular_quizzes');
    Route::get('/reports/questions-report', [ReportController::class, 'questionsReport'])->name('reports.questions_report');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
