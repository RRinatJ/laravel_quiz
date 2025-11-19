<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PopularQuizzesRequest;
use App\Http\Requests\QuestionsReportRequest;
use App\Services\ReportService;
use Inertia\Inertia;
use Inertia\Response;

final class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $service) {}

    public function popularQuizzes(PopularQuizzesRequest $request): Response
    {
        $stat = [];
        if ($request->has(['start', 'end'])) {
            $stat = $this->service->popularQuizzes(
                $request->string('start'),
                $request->string('end')
            );
        }

        return Inertia::render('reports/PopularQuizzesReport', [
            'data' => $stat,
            'filters' => $request->only(['start', 'end']),
        ]);
    }

    public function questionsReport(QuestionsReportRequest $request): Response
    {
        $stat = [];
        if ($request->has(['start', 'end'])) {
            $stat = $this->service->questionsReport(
                $request->string('start'),
                $request->string('end')
            );
        }

        return Inertia::render('reports/QuestionsReport', [
            'data' => $stat,
            'filters' => $request->only(['start', 'end']),
        ]);
    }
}
