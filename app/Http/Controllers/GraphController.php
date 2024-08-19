<?php

namespace App\Http\Controllers;

use App\Helpers\QuestionsHelper;
use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GraphController extends Controller
{
    protected $questionsHelper;

    public function __construct()
    {
        $this->questionsHelper = new QuestionsHelper;
    }

    public function index(Request $request)
    {
        $unit_id = $request->unit_id;
        session(['unit_id' => $unit_id]);

        $allQuestions = collect($this->questionsHelper->flattenQuestions());
        $scores = Score::where('unit_id', 1)->get()->keyBy('question_id');
        $groupedQuestions = $allQuestions->map(function ($item) use ($scores) {
            $questionId = $item['code'];
            return collect($item)
                ->put('code', $item['code'])
                ->put('score', $scores->has($questionId) ? $scores->get($questionId)->achieve_score : 0);
        })->groupBy('criteria');

        $tableData = $groupedQuestions->map(function ($questions, $criteria) {
            $totalScore = $questions->sum('score');
            $averageScore = number_format($questions->avg('score'), 2);

            if ($averageScore == 4) {
                $sebutan = 'Sangat Baik';
                $sebutanClass = 'bg-caribbean';
            } elseif ($averageScore >= 3) {
                $sebutan = 'Baik';
                $sebutanClass = 'bg-emerald-800';
            } elseif ($averageScore >= 2) {
                $sebutan = 'Cukup';
                $sebutanClass = 'bg-amber';
            } elseif ($averageScore >= 1) {
                $sebutan = 'Kurang';
                $sebutanClass = 'bg-[#FF9800]';
            } elseif ($averageScore >= 0) {
                $sebutan = 'Sangat Kurang';
                $sebutanClass = 'bg-[#D32F2F]';
            } else {
                $sebutan = '';
                $sebutanClass = '';
            }

            return [
                'criteria' => $criteria,
                'total_score' => number_format($totalScore, 2),
                'average_score' => $averageScore,
                'sebutan' => $sebutan,
                'sebutan_class' => $sebutanClass,
            ];
        })->values()->all();

        $saranPerbaikan = $allQuestions->filter(function ($question) use ($scores) {
            $score = $scores->has($question['code']) ? $scores->get($question['code'])->achieve_score : 0;
            return $score <= 2;
        })->map(function ($question) use ($scores) {
            $score = $scores->has($question['code']) ? $scores->get($question['code'])->achieve_score : 0;
            $questionText = $question['question'] ?? 'N/A';
            $mainQuestion = isset($question['question']['main']) ? $question['question']['main'] : $questionText;
            return [
                'question_code' => $question['code'],
                'question_text' => $mainQuestion,
                'score' => number_format($score, 2),
            ];
        })->values()->all();

        $sumAvg = number_format(collect($tableData)->avg('average_score'), 2);

        if ($sumAvg == 4) {
            $sebutan = 'Sangat Baik';
            $sebutanClass = 'bg-caribbean';
        } elseif ($sumAvg >= 3) {
            $sebutan = 'Baik';
            $sebutanClass = 'bg-emerald-800';
        } elseif ($sumAvg >= 2) {
            $sebutan = 'Cukup';
            $sebutanClass = 'bg-amber';
        } elseif ($sumAvg >= 1) {
            $sebutan = 'Kurang';
            $sebutanClass = 'bg-[#FF9800]';
        } elseif ($sumAvg >= 0) {
            $sebutan = 'Sangat Kurang';
            $sebutanClass = 'bg-[#D32F2F]';
        } else {
            $sebutan = '';
            $sebutanClass = '';
        }

        return view('results.graph')->with([
            'datas' => $tableData,
            'saran_perbaikan' => $saranPerbaikan,
            'sumAvg' => $sumAvg,
            'sebutan' => $sebutan,
            'sebutan_class' => $sebutanClass
        ]);
    }

    public function getChartData()
    {
        $allQuestions = collect($this->questionsHelper->flattenQuestions());
        $unit_id = session('unit_id');
        $scores = Score::where('unit_id', 1)->get()->keyBy('question_id');

        $groupedQuestions = $allQuestions->map(function ($item) use ($scores) {
            $questionId = $item['code'];
            return collect($item)
                ->put('code', $item['code'])
                ->put('weight', $this->questionsHelper->getWeight($item['code']))
                ->put('score', $scores->has($questionId) ? $scores->get($questionId)->achieve_score : 0);
        })->groupBy('criteria');

        $criteriaData = $groupedQuestions->map(function ($questions, $criteria) {
            return [
                'criteria' => $criteria,
                'labels' => $questions->pluck('code')->toArray(),
                'datasets' => [
                    [
                        'label' => $criteria,
                        'data' => $questions->pluck('score')->toArray(), // Menggunakan skor yang diambil
                        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                        'borderColor' => 'rgb(54, 162, 235)',
                        'pointBackgroundColor' => 'rgb(54, 162, 235)',
                        'pointBorderColor' => '#fff',
                        'pointHoverBackgroundColor' => '#fff',
                        'pointHoverBorderColor' => 'rgb(54, 162, 235)',
                    ]
                ]
            ];
        })->values()->all(); // Convert to array

        $allData = [
            'labels' => $groupedQuestions->flatMap(function ($questions) {
                return $questions->pluck('code');
            })->toArray(),
            'datasets' => [
                [
                    'label' => 'Peta Capaian',
                    'data' => $groupedQuestions->flatMap(function ($questions) {
                        return $questions->pluck('score');
                    })->toArray(),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1,
                ]
            ]
        ];

        return response()->json([
            'criteriaData' => $criteriaData,
            'allData' => [$allData],
        ]);
    }
}
