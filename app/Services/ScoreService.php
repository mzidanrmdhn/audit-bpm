<?php
namespace App\Services;

use App\Helpers\FormulasHelper;
use App\Helpers\QuestionsHelper;
use App\Models\Achievement;
use App\Models\Criteria;
use App\Models\Questions;
use App\Models\Score;
use App\Models\Target;
use App\Models\Unit;
use App\Models\Weight;
use Illuminate\Support\Arr;
use App\Traits\InstantServiceTrait;

class ScoreService {
    // use InstantServiceTrait;

    /**
     * Class model yang digunakan
     *
     * @var App\Models\Score
     */
    protected $model;

    /**
     * Path pagination
     *
     * @var string
     */
    protected $paginationPath = '/score/table';

    /**
     * List kolom yang akan ditampilkan
     *
     * @var array
     */
    protected $columns = [
    "unit_id",
    "question_id",
    "target_score",
    "achieve_score"
];

    /**
     * List kolom yang required ketika akan menyimpan data
     *
     * @var array
     */
    protected $columnsRequired = [
    "unit_id",
    "question_id",
    "target_score",
    "achieve_score"
];

    /**
     * @param App\Models\Score $model class
     */
    public function initModel()
    {
        $this->model = (new Score());
        return $this;
    }

    private $formulaService;
    private $formula;
    private $question;

    public function __construct(FormulaService $formulaService, FormulasHelper $formula, QuestionsHelper $question)
    {
        $this->formulaService = $formulaService;
        $this->formula = $formula;
        $this->question = $question;
    }

    public function getScoreData($unit_id, $user)
    {
        $criteria = Criteria::all();
        $questions = Questions::with('subCriteria')->get();
        $units = Unit::all();

        $questionCode = Questions::query()->pluck('code');
        $predAccreditation = $this->predAccreditation($unit_id);
        $generateWSM = $this->generateWSM($unit_id);
        $rankAccre = $this->accreditation($unit_id);
        $score = Score::where('unit_id', $unit_id)->with('question')->get()->map(function ($score) {
            return collect($score)->put('code', $score->question->code);
        })->keyBy('code');

        $displayedCodes = [];
        $tableData = [];
        $totalTerlampaui = 0;
        $totalTercapai = 0;
        $totalTidakTercapai = 0;

        $this->generate($unit_id);

        foreach ($questionCode as $code) {
            $shortCode = preg_replace('/[A-Z]$/', '', $code);
            if (!in_array($code, $displayedCodes)) {
                $displayedCodes[] = $code;
                $targetScore = $score[$code]['target_score'] ?? 0;
                $achieveScore = $score[$code]['achieve_score'] ?? 0;
                $predValue = $predAccreditation[0][$shortCode] ?? 'N/A';

                $sebutan = $this->getSebutan($achieveScore);
                $sebutanClass = $this->getSebutanClass($achieveScore);

                $ketercapaian = $this->getKetercapaian($targetScore, $achieveScore, $totalTerlampaui, $totalTercapai, $totalTidakTercapai);
                $ketercapaianClass = $this->getKetercapaianClass($targetScore, $achieveScore);

                $questionData = $questions->firstWhere('code', $code);
                $criteriaId = $questionData->subCriteria->criteria_id;
                $questionText = $questionData['main_question'] ?? 'N/A';

                $tableData[] = [
                    'code' => $code,
                    'question' => $questionText,
                    'criteria_id' => $criteriaId,
                    'target_score' => number_format($targetScore, 2),
                    'achieve_score' => number_format($achieveScore, 2),
                    'sebutan' => $sebutan,
                    'sebutan_class' => $sebutanClass,
                    'ketercapaian' => $ketercapaian,
                    'ketercapaian_class' => $ketercapaianClass,
                    'pred_value' => number_format((float)$predValue, 2)
                ];
            }
        }

        $totalData = count($tableData);
        $persentaseTerlampaui = $totalData ? ($totalTerlampaui / $totalData) * 100 : 0;
        $persentaseTercapai = $totalData ? ($totalTercapai / $totalData) * 100 : 0;
        $persentaseTidakTercapai = $totalData ? ($totalTidakTercapai / $totalData) * 100 : 0;
        return [
            'tableData' => $tableData,
            'persentaseTerlampaui' => number_format($persentaseTerlampaui, 2),
            'persentaseTercapai' => number_format($persentaseTercapai, 2),
            'persentaseTidakTercapai' => number_format($persentaseTidakTercapai, 2),
            'totalMaxPredAccreditation' => $predAccreditation['totalMaxPredAccreditation'],
            'accreditationScore' => $generateWSM['totalWeight'],
            'statusAccre' => $rankAccre['status'],
            'rankAccre' => $rankAccre['rank'],
            'criteria' => $criteria,
            'units' => $units
        ];
    }

    public function generate($unit_id)
    {
        $formulas = $this->formula->getFormula();
        $questions = Questions::pluck('code');
        $targetAnswers = Target::where('unit_id', $unit_id)->with('question')->get()->map(function ($answers) {
            return collect($answers)->put('code', $answers->question->code);
        })->keyBy('code');
        $achieveAnswers = Achievement::where('unit_id', $unit_id)->with('question')->get()->map(function ($answers) {
            return collect($answers)->put('code', $answers->question->code);
        })->keyBy('code');

        $targetResults = $this->formulaService->processFormula($formulas, $targetAnswers, $questions);
        $achieveResults = $this->formulaService->processFormula($formulas, $achieveAnswers, $questions);

        $results = [
            'targetResults' => $targetResults,
            'achieveResults' => $achieveResults
        ];

        $questions = Questions::all();

        // return $results['targetResults'];
        $questionTargetResult = collect($results['targetResults'])->map(function ($valuesCode, $questionCode) use ($questions) {
            $question = collect($questions)->where('code', $questionCode)->first();
            return collect($question)->put('target_score', $valuesCode);
        })->except([
            'IBIK-STD-02.1HASIL',
            'IBIK-STD-02.2HASIL',
            'IBIK-STD-03.3HASIL',
            'IBIK-STD-06.3HASIL',
            'IBIK-STD-02.4HASIL',
            'IBIK-STD-03.2HASIL',
            'IBIK-STD-04.15HASIL',
            'IBIK-STD-06.1HASIL',
            'IBIK-STD-06.7HASIL',
            'IBIK-STD-06.4HASIL',
            'IBIK-STD-06.10HASIL'
        ]);

        // $achieveValue = [];
        foreach ($questionTargetResult as $targetValue) {
            $achieveValue = Arr::get($achieveResults, $targetValue['code']);
            Score::updateOrCreate(
                [
                    'unit_id' => $unit_id,
                    'question_id' => $targetValue['id'],
                ],
                [
                    'target_score' => $targetValue['target_score'] ?? null,
                    'achieve_score' => $achieveValue ?? null
                ]
            );
        }

        // return $achieveValue;
        return redirect('/skor');
    }

    public function generateWSM($unit_id)
    {
        $weights = $this->weightedScore($unit_id);
        $formulas = $this->formula->getFormula();
        $bobot = Weight::with('question')->get();
        $bobot = collect($bobot)->keyBy('question.code')->map(fn($item) => $item['weight'])->toArray();

        $predAccreditation = [];
        $maxPredAccreditation = [];
        foreach (($formulas['formula2']) as $formula) {
            if (isset($weights[$formula[0]]) && isset($weights[$formula[1]])) {
                $a = $weights[$formula[0]]['weightedScore'];
                $b = $weights[$formula[1]]['weightedScore'];
                $key = substr($formula[0], 0, -1);
                $predAccreditation[$formula[0]] = $this->formulaService->formula2($a, $b);
                unset($weights[$formula[1]]);
                unset($bobot[$formula[1]]);
            }
        }

        if (isset($weights['IBIK-STD-02.4A']) && isset($weights['IBIK-STD-02.4B'])) {
            $a = $weights['IBIK-STD-02.4A']['weightedScore'];
            $b = $weights['IBIK-STD-02.4B']['weightedScore'];
            $predAccreditation['IBIK-STD-02.4A'] = ((2 * $a) + $b)/3;
            unset($weights['IBIK-STD-02.4B']);
            unset($bobot['IBIK-STD-02.4A'], $bobot['IBIK-STD-02.4B']);
        }

        if (isset($weights['IBIK-STD-03.2A']) && isset($weights['IBIK-STD-03.2B'])) {
            $a = $weights['IBIK-STD-03.2A']['weightedScore'];
            $b = $weights['IBIK-STD-03.2B']['weightedScore'];
            $predAccreditation['IBIK-STD-03.2A'] = $this->formulaService->formula5($a, $b);
            unset($weights['IBIK-STD-03.2B']);
            unset($bobot['IBIK-STD-03.2A'], $bobot['IBIK-STD-03.2B']);
        }

        foreach (($formulas['formula19']) as $formula) {
            if (isset($weights[$formula[0]]) && isset($weights[$formula[1]])) {
                $a = $weights[$formula[0]]['weightedScore'];
                $b = $weights[$formula[1]]['weightedScore'];
                $key = substr($formula[0], 0, -1);
                $predAccreditation[$formula[0]] = $this->formulaService->formula19($a, $b);
                $maxPredAccreditation[$key] = $this->formulaService->formula19(($bobot[$formula[0]] * 4), ($bobot[$formula[1]] * 4));
                unset($weights[$formula[1]]);
                unset($bobot[$formula[1]]);
            }
        }

        foreach (($formulas['formula24']) as $formula) {
            if (isset($weights[$formula[0]]) && isset($weights[$formula[1]])&& isset($weights[$formula[2]])) {
                $a = $weights[$formula[0]]['weightedScore'];
                $b = $weights[$formula[1]]['weightedScore'];
                $c = $weights[$formula[2]]['weightedScore'];
                $key = substr($formula[0], 0, -1);
                $predAccreditation[$formula[0]] = $this->formulaService->formula24($a, $b, $c);
                $maxPredAccreditation[$key] = $this->formulaService->formula24(($bobot[$formula[0]] * 4), ($bobot[$formula[1]] * 4), ($bobot[$formula[2]] * 4));
                unset($weights[$formula[1]], $weights[$formula[2]]);
                unset($bobot[$formula[1]], $bobot[$formula[2]]);
            }
        }

        foreach (($formulas['formula25']) as $formula) {
            if (isset($weights[$formula[0]]) && isset($weights[$formula[1]]) && isset($weights[$formula[2]]) && isset($weights[$formula[3]]) && isset($weights[$formula[4]])) {
                $a = $weights[$formula[0]]['weightedScore'];
                $b = $weights[$formula[1]]['weightedScore'];
                $c = $weights[$formula[2]]['weightedScore'];
                $d = $weights[$formula[3]]['weightedScore'];
                $e = $weights[$formula[4]]['weightedScore'];
                $key = substr($formula[0], 0, -1);
                $predAccreditation[$formula[0]] = $this->formulaService->formula25($a, $b, $c, $d, $e);
                $maxPredAccreditation[$key] = $this->formulaService->formula25(($bobot[$formula[0]] * 4), ($bobot[$formula[1]] * 4), ($bobot[$formula[2]] * 4), ($bobot[$formula[3]] * 4), ($bobot[$formula[4]] * 4));
                unset($weights[$formula[1]], $weights[$formula[2]], $weights[$formula[3]], $weights[$formula[4]]);
                unset($bobot[$formula[1]], $bobot[$formula[2]], $bobot[$formula[3]], $bobot[$formula[4]]);
            }
        }

        foreach (($formulas['formula28']) as $formula) {
            if (isset($weights[$formula[0]]) && isset($weights[$formula[1]])) {
                $a = $weights[$formula[0]]['weightedScore'];
                $b = $weights[$formula[1]]['weightedScore'];
                $key = substr($formula[0], 0, -1);
                $predAccreditation[$formula[0]] = $this->formulaService->formula28($a, $b);
                $maxPredAccreditation[$key] = $this->formulaService->formula28(($bobot[$formula[0]] * 4), ($bobot[$formula[1]] * 4));
                unset($weights[$formula[1]]);
                unset($bobot[$formula[1]]);
            }
        }


        foreach ($weights as $key => $value) {
            if (!isset($predAccreditation[$key])) {
                $predAccreditation[$key] = $value['weightedScore'];
            }
        }

        $questions = Questions::with('subCriteria.criteria')->get();
        $groupedResults = [];
        foreach ($questions as $question) {
            $criteriaName = $question->subCriteria->criteria->name;
            $questionCode = $question->code;

            if (!isset($groupedResults[$criteriaName])) {
                $groupedResults[$criteriaName] = [
                    'weight' => 0
                ];
            }

            if (isset($predAccreditation[$questionCode])) {
                $groupedResults[$criteriaName]['weight'] += $predAccreditation[$questionCode];
            }
        }

        $totalWeight = 0;
        foreach ($groupedResults as $criteriaName => &$result) {
            $result['weight'] = round($result['weight'], 2);
            $totalWeight += $result['weight'];
        }

        return [
            'groupedResults' => $groupedResults,
            'totalWeight' => round($totalWeight, 2)
        ];
    }

    public function accreditation($unit_id)
    {
        $score = Score::where('unit_id', $unit_id)->with('question')->get();
        $achieveScore = collect($score)->keyBy('question.code')->map(fn($item) => $item['achieve_score'])->toArray();
        $accreScore = $this->generateWSM($unit_id)['totalWeight'];

        $tresholdUnggul = 3.5;
        $tresholdBaikSekali = 3.0;
        if (($achieveScore['IBIK-STD-04.2'] >= $tresholdUnggul) && ($achieveScore['IBIK-STD-04.3'] >= $tresholdUnggul) && ($achieveScore['IBIK-STD-09.9'] >= $tresholdUnggul) && ($achieveScore['IBIK-STD-09.10'] >= $tresholdUnggul)) {
            $tempRank = 'Unggul';
        } elseif (($achieveScore['IBIK-STD-04.2'] >= $tresholdBaikSekali) && ($achieveScore['IBIK-STD-04.3'] >= $tresholdBaikSekali) && ($achieveScore['IBIK-STD-09.9'] >= $tresholdBaikSekali) && ($achieveScore['IBIK-STD-09.10'] >= $tresholdBaikSekali)) {
            $tempRank = 'Baik Sekali';
        } else {
            $tempRank = 'Baik';
        }

        $tresholdAccre = 2.0;
        $pointThree = ($achieveScore['IBIK-STD-06.1A'] + (2 * $achieveScore['IBIK-STD-06.1B']) + (2 * $achieveScore['IBIK-STD-06.1C']))/5;
        if (($achieveScore['IBIK-STD-02.7'] >= $tresholdAccre) && ($achieveScore['IBIK-STD-04.1'] >= $tresholdAccre) && ($pointThree >= $tresholdAccre)) {
            $accre = true;
        } else {
            $accre = false;
        }

        if ($accreScore >= 361) {
            if ($accre) {
                $status = 'Terakreditasi';
                $rank = $tempRank ? 'Unggul' : 'Baik Sekali';
            } else {
                $status = 'Tidak Terakreditasi';
                $rank = '-';
            }
        } elseif ($accreScore >= 301) {
            if ($accre) {
                $status = 'Terakreditasi';
                $rank = $tempRank ? 'Baik Sekali' : 'Baik';
            } else {
                $status = 'Tidak Terakreditasi';
                $rank = '-';
            }
        } elseif ($accreScore >= 200) {
            if ($accre) {
                $rank = 'Baik';
                $status = 'Terakreditasi';
            } else {
                $status = 'Tidak Terakreditasi';
                $rank = '-';
            }
        } else {
            $status = 'Tidak Terakreditasi';
            $rank = '-';
        }

        return [
            'status' => $status,
            'rank' => $rank
        ];
    }

    public function predAccreditation($unit_id)
    {
        $weights = $this->weightedScore($unit_id);
        $formulas = $this->formula->getFormula();
        $bobot = Weight::with('question')->get();
        $bobot = collect($bobot)->keyBy('question.code')->map(fn($item) => $item['weight'])->toArray();

        $predAccreditation = [];
        $maxPredAccreditation = [];
        foreach (($formulas['formula2']) as $formula) {
            if (isset($weights[$formula[0]]) && isset($weights[$formula[1]])) {
                $a = $weights[$formula[0]]['weightedScore'];
                $b = $weights[$formula[1]]['weightedScore'];
                $key = substr($formula[0], 0, -1);
                $predAccreditation[$key] = $this->formulaService->formula2($a, $b);
                $maxPredAccreditation[$key] = $this->formulaService->formula2(($bobot[$formula[0]] * 4), ($bobot[$formula[1]] * 4));
                unset($weights[$formula[1]]);
                unset($bobot[$formula[1]]);
            }
        }

        if (isset($weights['IBIK-STD-02.4A']) && isset($weights['IBIK-STD-02.4B'])) {
            $a = $weights['IBIK-STD-02.4A']['weightedScore'];
            $b = $weights['IBIK-STD-02.4B']['weightedScore'];
            $predAccreditation['IBIK-STD-02.4'] = ((2 * $a) + $b)/3;
            $maxPredAccreditation['IBIK-STD-02.4'] = ((2 * $bobot['IBIK-STD-02.4A']) + $bobot['IBIK-STD-02.4B']) / 3;
            unset($weights['IBIK-STD-02.4A'], $weights['IBIK-STD-02.4B']);
            unset($bobot['IBIK-STD-02.4B']);
        }

        if (isset($weights['IBIK-STD-03.2A']) && isset($weights['IBIK-STD-03.2B'])) {
            $a = $weights['IBIK-STD-03.2A']['weightedScore'];
            $b = $weights['IBIK-STD-03.2B']['weightedScore'];
            $predAccreditation['IBIK-STD-03.2'] = $this->formulaService->formula5($a, $b);
            $maxPredAccreditation['IBIK-STD-03.2'] = ((2 * $bobot['IBIK-STD-03.2A']) + $bobot['IBIK-STD-03.2B']) / 3;
            unset($weights['IBIK-STD-03.2A'], $weights['IBIK-STD-03.2B']);
            unset($bobot['IBIK-STD-03.2B']);
        }

        foreach (($formulas['formula19']) as $formula) {
            if (isset($weights[$formula[0]]) && isset($weights[$formula[1]])) {
                $a = $weights[$formula[0]]['weightedScore'];
                $b = $weights[$formula[1]]['weightedScore'];
                $key = substr($formula[0], 0, -1);
                $predAccreditation[$key] = $this->formulaService->formula19($a, $b);
                $maxPredAccreditation[$key] = $this->formulaService->formula19(($bobot[$formula[0]] * 4), ($bobot[$formula[1]] * 4));
                unset($weights[$formula[0]], $weights[$formula[1]]);
                unset($bobot[$formula[1]]);
            }
        }

        foreach (($formulas['formula24']) as $formula) {
            if (isset($weights[$formula[0]]) && isset($weights[$formula[1]])&& isset($weights[$formula[2]])) {
                $a = $weights[$formula[0]]['weightedScore'];
                $b = $weights[$formula[1]]['weightedScore'];
                $c = $weights[$formula[2]]['weightedScore'];
                $key = substr($formula[0], 0, -1);
                $predAccreditation[$key] = $this->formulaService->formula24($a, $b, $c);
                $maxPredAccreditation[$key] = $this->formulaService->formula24(($bobot[$formula[0]] * 4), ($bobot[$formula[1]] * 4), ($bobot[$formula[2]] * 4));
                unset($weights[$formula[0]], $weights[$formula[1]], $weights[$formula[2]]);
                unset($bobot[$formula[1]], $bobot[$formula[2]]);
            }
        }

        foreach (($formulas['formula25']) as $formula) {
            if (isset($weights[$formula[0]]) && isset($weights[$formula[1]]) && isset($weights[$formula[2]]) && isset($weights[$formula[3]]) && isset($weights[$formula[4]])) {
                $a = $weights[$formula[0]]['weightedScore'];
                $b = $weights[$formula[1]]['weightedScore'];
                $c = $weights[$formula[2]]['weightedScore'];
                $d = $weights[$formula[3]]['weightedScore'];
                $e = $weights[$formula[4]]['weightedScore'];
                $key = substr($formula[0], 0, -1);
                $predAccreditation[$key] = $this->formulaService->formula25($a, $b, $c, $d, $e);
                $maxPredAccreditation[$key] = $this->formulaService->formula25(($bobot[$formula[0]] * 4), ($bobot[$formula[1]] * 4), ($bobot[$formula[2]] * 4), ($bobot[$formula[3]] * 4), ($bobot[$formula[4]] * 4));
                unset($weights[$formula[0]], $weights[$formula[1]], $weights[$formula[2]], $weights[$formula[3]], $weights[$formula[4]]);
                unset($bobot[$formula[1]], $bobot[$formula[2]], $bobot[$formula[3]], $bobot[$formula[4]]);
            }
        }

        foreach (($formulas['formula28']) as $formula) {
            if (isset($weights[$formula[0]]) && isset($weights[$formula[1]])) {
                $a = $weights[$formula[0]]['weightedScore'];
                $b = $weights[$formula[1]]['weightedScore'];
                $key = substr($formula[0], 0, -1);
                $predAccreditation[$key] = $this->formulaService->formula28($a, $b);
                $maxPredAccreditation[$key] = $this->formulaService->formula28(($bobot[$formula[0]] * 4), ($bobot[$formula[1]] * 4));
                unset($weights[$formula[0]], $weights[$formula[1]]);
                unset($bobot[$formula[1]]);
            }
        }

        foreach ($weights as $key => $value) {
            $predAccreditation[$key] = $value['weightedScore'];
        }

        $totalMaxPredAccreditation = 0;
        foreach ($bobot as $key => $value) {
            $maxPredAccreditation[$key] = $value*4;
            $totalMaxPredAccreditation += $value*4;
        }

        return [
            $predAccreditation,
            $maxPredAccreditation,
            'totalMaxPredAccreditation' => round($totalMaxPredAccreditation, 2)
        ];
    }

    public function weightedScore($unit_id)
    {
        $score = Score::where('unit_id', $unit_id)->with('question')->get();
        $weight = Weight::all();

        $weightedScore = collect($score)->map(function($scoreValue) use ($weight) {
            $bobot = collect($weight)->where('question_id', $scoreValue->question_id)->first();
            return [
                'question_id' => $scoreValue->question->code,
                'weightedScore' => $scoreValue->achieve_score * $bobot->weight,
            ];
        })->keyBy('question_id');

        return $weightedScore;
    }

    protected function getSebutan($achieveScore)
    {
        if ($achieveScore == 4) {
            return 'Sangat Baik';
        } elseif ($achieveScore >= 3) {
            return 'Baik';
        } elseif ($achieveScore >= 2) {
            return 'Cukup';
        } elseif ($achieveScore >= 1) {
            return 'Kurang';
        } elseif ($achieveScore >= 0) {
            return 'Sangat Kurang';
        } else {
            return '';
        }
    }

    protected function getSebutanClass($achieveScore)
    {
        if ($achieveScore == 4) {
            return 'caribbean';
        } elseif ($achieveScore >= 3) {
            return 'teal';
        } elseif ($achieveScore >= 2) {
            return 'amber';
        } elseif ($achieveScore >= 1) {
            return '[#FF9800]';
        } elseif ($achieveScore >= 0) {
            return '[#D32F2F]';
        } else {
            return '';
        }
    }

    protected function getKetercapaian($targetScore, $achieveScore, &$totalTerlampaui, &$totalTercapai, &$totalTidakTercapai)
    {
        if ($targetScore > $achieveScore || ($targetScore == 0 && $achieveScore == 0)) {
            $totalTidakTercapai++;
            return 'Tidak Tercapai';
        } elseif ($targetScore == $achieveScore) {
            $totalTercapai++;
            return 'Tercapai';
        } elseif ($targetScore < $achieveScore) {
            $totalTerlampaui++;
            return 'Terlampaui';
        } else {
            return '';
        }
    }

    protected function getKetercapaianClass($targetScore, $achieveScore)
    {
        if ($targetScore > $achieveScore || ($targetScore == 0 && $achieveScore == 0)) {
            return '[#D32F2F]';
        } elseif ($targetScore == $achieveScore) {
            return 'cerulean';
        } elseif ($targetScore < $achieveScore) {
            return 'caribbean';
        } else {
            return '';
        }
    }
}
