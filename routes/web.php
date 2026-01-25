<?php

declare(strict_types=1);

use App\Http\Controllers\AiController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SelectQuizController;
use App\Http\Controllers\Telegram\TelegramController;
use App\Http\Controllers\TmdbImageController;
use Illuminate\Support\Facades\Route;

Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])->name('telegram.webhook');
Route::get('/', SelectQuizController::class)->name('home');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/game/{quiz_id}', [GameController::class, 'store'])->name('game.create');
Route::get('/game/questions/{game_id}', [GameController::class, 'show'])->name('game.show');
Route::post('/game/{game_id}', [GameController::class, 'edit'])->name('game.edit');
Route::get('/game/set_update/{game_id}', [GameController::class, 'setUpdate'])->name('game.set_update');

Route::get('/articles/{article:slug}', [ArticleController::class, 'by_slug'])->name('article.slug');

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

    Route::resource('article', ArticleController::class)->except([
        'update', 'show',
    ]);
    Route::post('/article/{article}', [ArticleController::class, 'update'])->name('article.update');

    Route::prefix('tmdb')->name('tmdb.')->group(function (): void {
        Route::get('/search', [TmdbImageController::class, 'search'])->name('search');
        Route::get('/images', [TmdbImageController::class, 'images'])->name('images');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
