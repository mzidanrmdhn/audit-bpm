<?php
namespace App\Services;

use App\Models\Graph;
use App\Models\Score;
use App\Models\Questions;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use App\Traits\InstantServiceTrait;

class GraphService {
    // use InstantServiceTrait;

    /**
     * Class model yang digunakan
     *
     * @var App\Models\Graph
     */
    protected $model;

    /**
     * Path pagination
     *
     * @var string
     */
    protected $paginationPath = '/graph/table';

    /**
     * List kolom yang akan ditampilkan
     *
     * @var array
     */
    protected $columns = [];

    /**
     * List kolom yang required ketika akan menyimpan data
     *
     * @var array
     */
    protected $columnsRequired = [];

    /**
     * @param App\Models\Graph $model class
     */
    public function initModel()
    {
        $this->model = (new Graph());
        return $this;
    }

    public function getTableData($unit_id)
    {
        $units = Unit::all();
        $questions = Questions::with('subCriteria')->get();
        $score = Score::where('unit_id', $unit_id)->with('question')->get()->map(function ($score) {
            return collect($score)->put('code', $score->question->code);
        })->keyBy('code');

        $groupedQuestions = $questions->map(function ($item) use ($score) {
            $questionId = $item['code'];
            return collect($item)
                ->put('code', $item['code'])
                ->put('achieve_score', $score->has($questionId) ? $score->get($questionId)['achieve_score'] : 0)
                ->put('criteria', $item->subCriteria->criteria->name);
        })->groupBy('criteria');

        $tableData = $groupedQuestions->map(function ($questions, $criteria) {
            $totalScore = $questions->sum('achieve_score');
            $averageScore = number_format($questions->avg('achieve_score'), 2);

            $sebutan = $this->getSebutan($averageScore);
            $sebutanClass = $this->getSebutanClass($averageScore);

            return [
                'criteria' => $criteria,
                'total_score' => number_format($totalScore, 2),
                'average_score' => $averageScore,
                'sebutan' => $sebutan,
                'sebutan_class' => $sebutanClass,
            ];
        })->values()->all();

        $saranPerbaikan = $questions->filter(function ($question) use ($score) {
            $score = $score->has($question['code']) ? $score->get($question['code'])['achieve_score'] : 0;
            return $score <= 2;
        })->map(function ($question) use ($score) {
            $score = $score->has($question['code']) ? $score->get($question['code'])['achieve_score'] : 0;
            $questionText = $question['main_question'] ?? 'N/A';
            return [
                'question_code' => $question['code'],
                'question_text' => $questionText,
                'score' => number_format($score, 2),
            ];
        })->values()->all();

        $sumAvg = number_format(collect($tableData)->avg('average_score'), 2);
        $sebutan = $this->getSebutan($sumAvg);
        $sebutanClass = $this->getSebutanClass($sumAvg);

        return [
            'datas' => $tableData,
            'saran_perbaikan' => $saranPerbaikan,
            'sumAvg' => $sumAvg,
            'sebutan' => $sebutan,
            'sebutan_class' => $sebutanClass,
            'units' => $units
        ];
    }

    public function getChartData($unit_id)
    {
        $questions = Questions::all();
        $score = Score::where('unit_id', $unit_id)->with('question')->get()->map(function ($score) {
            return collect($score)->put('code', $score->question->code);
        })->keyBy('code');
        $groupedQuestions = $questions->map(function ($item) use ($score) {
            $questionId = $item['code'];
            return collect($item)
                ->put('code', $item['code'])
                ->put('achieve_score', $score->has($questionId) ? $score->get($questionId)['achieve_score'] : 0)
                ->put('criteria', $item->subCriteria->criteria->name);
        })->groupBy('criteria');

        $criteriaData = $groupedQuestions->map(function ($questions, $criteria) {
            return [
                'criteria' => $criteria,
                'labels' => $questions->pluck('code')->toArray(),
                'datasets' => [
                    [
                        'label' => $criteria,
                        'data' => $questions->pluck('achieve_score')->toArray(),
                        'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                        'borderColor' => 'rgb(54, 162, 235)',
                        'pointBackgroundColor' => 'rgb(54, 162, 235)',
                        'pointBorderColor' => '#fff',
                        'pointHoverBackgroundColor' => '#fff',
                        'pointHoverBorderColor' => 'rgb(54, 162, 235)',
                    ]
                ]
            ];
        })->values()->all();

        $allData = [
            'labels' => $groupedQuestions->flatMap(function ($questions) {
                return $questions->pluck('code');
            })->toArray(),
            'datasets' => [
                [
                    'label' => 'Peta Capaian',
                    'data' => $groupedQuestions->flatMap(function ($questions) {
                        return $questions->pluck('achieve_score');
                    })->toArray(),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1,
                ]
            ]
        ];

        return [
            'criteriaData' => $criteriaData,
            'allData' => [$allData],
        ];
    }

    private function getSebutan($averageScore)
    {
        if ($averageScore == 4) return 'Sangat Baik';
        if ($averageScore >= 3) return 'Baik';
        if ($averageScore >= 2) return 'Cukup';
        if ($averageScore >= 1) return 'Kurang';
        return 'Sangat Kurang';
    }

    private function getSebutanClass($averageScore)
    {
        if ($averageScore == 4) return 'bg-caribbean';
        if ($averageScore >= 3) return 'bg-emerald-800';
        if ($averageScore >= 2) return 'bg-amber';
        if ($averageScore >= 1) return 'bg-[#FF9800]';
        return 'bg-[#D32F2F]';
    }
}
