<?php

declare(strict_types=1);

use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::get('dashboard', fn () => Inertia::render('Dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::resource('quiz', QuizController::class)->except([
        'update', 'show',
    ]);
    Route::post('/quiz/{quiz}', [QuizController::class, 'update'])->name('quiz.update');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
