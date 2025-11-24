<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Game;
use App\Models\GameStep;
use Illuminate\Database\Eloquent\Collection;
use Stringable;

final class ReportService
{
    public function popularQuizzes(Stringable $start, Stringable $end): Collection
    {
        $query = Game::query()
            ->whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59']);

        $stat = (clone $query)
            ->select('quiz_id', 'id')
            ->selectRaw('
                COUNT(*) as play_count, 
                COUNT(CASE WHEN user_id IS NULL THEN id END) unregistered_count,  
                COUNT(CASE WHEN user_id IS NOT NULL THEN id END) registered_count
            ')
            ->groupBy('quiz_id')
            ->orderByDesc('play_count')
            ->with('quiz:id,title')
            ->get();

        $win_counts = (clone $query)
            ->whereHas('latestStep', function ($query): void {
                $query->where(function ($q): void {
                    $q->where('is_correct', true)
                        ->orWhere('can_skip', true);
                })->whereColumn('games.current_question_id', 'game_steps.question_id');
            })
            ->select('quiz_id')
            ->selectRaw('COUNT(*) as win_count')
            ->groupBy('quiz_id')
            ->get();

        $stat->map(function ($item) use ($win_counts) {
            $win_count = $win_counts->firstWhere('quiz_id', $item->quiz_id);
            $item->win_count = $win_count ? $win_count->win_count : 0;

            return $item;
        });

        return $stat;
    }

    public function questionsReport(Stringable $start, Stringable $end): Collection
    {
        $query = GameStep::query()
            ->whereBetween('created_at', [$start.' 00:00:00', $end.' 23:59:59']);

        return $query
            ->select('question_id', 'id')
            ->selectRaw('
                COUNT(*) as play_count, 
                COUNT(CASE WHEN is_correct = true THEN id END) corrected_count
            ')
            ->groupBy('question_id')
            ->orderByDesc('play_count')
            ->with('question:id,question,image,audio')
            ->limit(25)
            ->get();
    }
}
