<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\InterviewSchedule;
use App\Models\Interview;

use App\Models\User;

class ValidityController extends Controller
{
    public function index()
    {
        $interviews = InterviewSchedule::with([
            'interview' => function ($query) {
                $query->with(['selectedTestData']);
            },
        ])
            ->has('interview')
            ->get();

        $pointsMapping = [
            'tidak setuju' => 1,
            'kurang setuju' => 2,
            'cukup setuju' => 3,
            'setuju' => 4,
            'sangat setuju' => 5,
            'tidak puas' => 1,
            'kurang puas' => 2,
            'cukup puas' => 3,
            'puas' => 4,
            'sangat puas' => 5,
            'tidak berhasil' => 1,
            'kurang berhasil' => 2,
            'cukup berhasil' => 3,
            'berhasil' => 4,
            'sangat berhasil' => 5,
        ];

        // Mengumpulkan respondent_answer, question_id, dan points
        $results = $interviews->flatMap(function ($schedule) use ($pointsMapping) {
            return $schedule->interview->selectedTestData->map(function ($data) use ($pointsMapping) {
                $lowercasedAnswer = strtolower($data->respondent_answer);
                return [
                    'interview_id' => $data->interview_id,
                    'question_id' => $data->question_id,
                    'respondent_answer' => $data->respondent_answer,
                    'points' => $pointsMapping[$lowercasedAnswer] ?? 0,
                ];
            });
        });

        // Mengelompokkan poin berdasarkan question_id dan menjumlahkan
        $summedXPoints = $results->groupBy('question_id')->map(function ($group) {
            return $group->sum('points');
        });
        $summedSquaredXPoints = $results->groupBy('question_id')->map(function ($group) {
            $sum = $group->sum('points');
            return pow($sum, 2);
        });
        // Mengelompokkan poin berdasarkan question_id, mempangkatkan setiap poin, dan menjumlahkan
        $squaredXSummedPoints = $results->groupBy('question_id')->map(function ($group) {
            return $group->reduce(function ($carry, $item) {
                return $carry + pow($item['points'], 2);
            }, 0);
        });

        // Mengelompokkan poin berdasarkan interview id dan menjumlahkan
        $summedPointsByInterview = $results->groupBy('interview_id')->map(function ($group) {
            return $group->sum('points');
        });

        $summedYPoints = $summedPointsByInterview->sum();

        // Mengelompokkan poin berdasarkan id dan menjumlahkan
        $calculatedXY = $results->map(function ($item) use ($summedPointsByInterview) {
            $summedY = $summedPointsByInterview[$item['interview_id']] ?? 0;
            return [
                'question_id' => $item['question_id'],
                'respondent_answer' => $item['respondent_answer'],
                'points' => $item['points'],
                'interview_id' => $item['interview_id'],
                'x*y' => $item['points'] * $summedY,
            ];
        });
        $summedCalculatedXY = $calculatedXY->groupBy('question_id')->map(function ($group) {
            return $group->sum('x*y');
        });

        dd($results, $summedXPoints, $squaredXSummedPoints, $summedPointsByInterview, $summedYPoints, $calculatedXY, $summedCalculatedXY, $summedSquaredXPoints);

        // Mengembalikan data (opsional, ini tidak akan pernah dijalankan karena dd() menghentikan eksekusi)

        return $interviews;
    }
}
