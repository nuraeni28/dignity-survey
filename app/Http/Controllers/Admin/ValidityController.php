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

        // Menghitung jumlah interview
        $countInterview = $interviews->count();
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

        //Mengelompokkan poin berdasarkan question_id, menjumlahkan setiap point dan pangkat 2 hasil jumlahnya
        $summedSquaredXPoints = $results->groupBy('question_id')->map(function ($group) {
            $sum = $group->sum('points');
            return pow($sum, 2);
        });

        // Mengelompokkan poin berdasarkan question_id, pangkat 2 setiap poin, dan menjumlahkan
        $squaredXSummedPoints = $results->groupBy('question_id')->map(function ($group) {
            return $group->reduce(function ($carry, $item) {
                return $carry + pow($item['points'], 2);
            }, 0);
        });

        // Mengelompokkan poin berdasarkan interview id dan menjumlahkan
        $summedPointsByInterview = $results->groupBy('interview_id')->map(function ($group) {
            return $group->sum('points');
        });

        // Menjumlahkan semua point pada summedPointsByInterview
        $summedYPoints = $summedPointsByInterview->sum();

        // Menghitung jumlah pangkat 2 setiap poin pada summedPointsByInterview
        $squaredYSummedPoints = $summedPointsByInterview
            ->map(function ($points) {
                return $points * $points;
            })
            ->sum();

        //Pangkat 2 dari squaredYSummedPoints
        $summedSquaredYPoints = $summedYPoints * $summedYPoints;

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

        // Menghitung validitas untuk setiap question_id
        $validity = $summedXPoints->map(function ($sumX, $questionId) use ($summedYPoints, $squaredYSummedPoints, $summedXPoints, $summedCalculatedXY, $countInterview, $squaredXSummedPoints, $summedSquaredYPoints, $summedSquaredXPoints) {
            $sumX = $summedXPoints[$questionId] ?? 0;
            $sumXY = $summedCalculatedXY[$questionId] ?? 0;
            $squaredX = $squaredXSummedPoints[$questionId] ?? 0;
            $summedSquaredX = $summedSquaredXPoints[$questionId] ?? 0;

            $numerator = 10 * $sumXY - $sumX * $summedYPoints;
            $denominator = (10 * $squaredX - $summedSquaredX) * (10 * $squaredYSummedPoints - $summedSquaredYPoints);

            $calculationProcess = "(10 * $sumXY - $sumX * $summedYPoints) / (10 * $squaredX - $summedSquaredX) * (10 * $squaredYSummedPoints - $summedSquaredYPoints))";

            echo "Question ID: $questionId\n";
            echo "Calculation: $calculationProcess\n";

            $result = $numerator / sqrt($denominator);

            echo "Result (sqrt): $result\n";

            return $result;
        });

        dd($validity, $countInterview, $results, $summedXPoints, $squaredXSummedPoints, $summedPointsByInterview, $summedYPoints, $squaredYSummedPoints, $summedSquaredYPoints, $calculatedXY, $summedCalculatedXY, $summedSquaredXPoints);

        // Mengembalikan data (opsional, ini tidak akan pernah dijalankan karena dd() menghentikan eksekusi)

        return $interviews;
    }
}
