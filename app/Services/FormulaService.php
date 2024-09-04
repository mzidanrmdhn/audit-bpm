<?php
namespace App\Services;

use App\Models\Formula;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Traits\InstantServiceTrait;

class FormulaService {
    // use InstantServiceTrait;

    /**
     * Class model yang digunakan
     *
     * @var App\Models\Formula
     */
    protected $model;

    /**
     * Path pagination
     *
     * @var string
     */
    protected $paginationPath = '/formula/table';

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
     * @param App\Models\Formula $model class
     */
    public function initModel()
    {
        // $this->model = (new Formula());
        return $this;
    }

    public function processFormula(Collection $formulas, $answers, $questionsCode)
    {
        $results = [];

        $questions = collect($questionsCode)->map(function ($questionCode, $item) {
            return [
                'question_code' => $questionCode,
                'target_answer' => 0,
                'achievement_answer' => 0
            ];
        })->keyBy('question_code')->toArray();
        $answers = collect($answers)->map(fn($item) => collect($item)->put('question_id', $item['code']));
        // Formula 1
        $formula1Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula1'));
        })->toArray();

        $formula1Answers = array_merge(
            collect($questions)->only(Arr::get($formulas, 'formula1'))->toArray(),
            $formula1Answers
        );

        foreach ($formula1Answers as $questionCode => $answer) {
            $data = collect($answer)->get('target_answer') ?? collect($answer)->get('achievement_answer');
            $results[$questionCode] = $data;
        }

        // Formula 2
        $formula2Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), collect(Arr::get($formulas, 'formula2'))->collapse()->toArray());
        });

        foreach (Arr::get($formulas, 'formula2') as $formula) {
            $valueA = isset($formula2Answers[$formula[0]]) ? ($formula2Answers[$formula[0]]['target_answer'] ?? $formula2Answers[$formula[0]]['achievement_answer']) : 0;
            $valueB = isset($formula2Answers[$formula[1]]) ? ($formula2Answers[$formula[1]]['target_answer'] ?? $formula2Answers[$formula[1]]['achievement_answer']) : 0;
            $hasilKey = substr($formula[0], 0, -1) . 'HASIL';
            $hasil = $this->formula2(
                $valueA,
                $valueB,
            );
            $results[$formula[0]] = $valueA;
            $results[$formula[1]] = $valueB;
            $results[$hasilKey] = $hasil;
        }

        // Formula 3

        // $formula3Answers = collect($answers)->filter(fn($item) => in_array($item['question_id'], Arr::get($formulas, 'formula3')));

        $formula3Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula3'));
        });
        foreach (Arr::get($formulas, 'formula3') as $formula) {
            $answerA = $formula3Answers->firstWhere('question_id', 'IBIK-STD-02.4A');
            $achievementAnswerA = isset($answerA['achievement_answer']) ? ($answerA['target_answer'] ?? $answerA['achievement_answer']) : 0;
            $dataA = json_decode($achievementAnswerA, true);
            $n1 = isset($dataA['N1']) ? ($dataA['N1']) : 0;
            $n2 = isset($dataA['N2']) ? ($dataA['N2']) : 0;
            $n3 = isset($dataA['N3']) ? ($dataA['N3']) : 0;
            $ndtps = isset($dataA['NDTPS']) ? ($dataA['NDTPS']) : 0;
            $skor3A = $this->formula3A($n1, $n2, $n3, $ndtps);
            $results['IBIK-STD-02.4A'] = $skor3A;

            $answerB = $formula3Answers->firstWhere('question_id', 'IBIK-STD-02.4B');
            $achievementAnswerB = isset($answerB['achievement_answer']) ? ($answerB['target_answer'] ?? $answerB['achievement_answer']) : 0;
            $dataB = json_decode($achievementAnswerB, true);
            $ni = isset($dataB['NI']) ? ($dataB['NI']) : 0;
            $nn = isset($dataB['NN']) ? ($dataB['NN']) : 0;
            $nw = isset($dataB['NW']) ? ($dataB['NW']) : 0;
            $skor3B = $this->formula3B($ni, $nn, $nw);
            $results['IBIK-STD-02.4B'] = $skor3B;

            $hasil = ((2 * $skor3A) + $skor3B)/3;
            $hasilKey = substr($formula, 0, -1) . 'HASIL';
            $results[$hasilKey] = $hasil;
        }

        // Formula 4
        $formula4Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula4'));
        });
        $answer = $formula4Answers->firstWhere('question_id', 'IBIK-STD-03.1');
        $achievementAnswer = isset($answer['achievement_answer']) ? ($answer['target_answer'] ?? $answer['achievement_answer']) : 0;
        $data = json_decode($achievementAnswer, true);
        $jp = isset($data['JP']) ? ($data['JP']) : 0;
        $jpl = isset($data['JPL']) ? ($data['JPL']) : 0;

        $hasil = $this->formula4($jp, $jpl);

        $results['IBIK-STD-03.1'] = $hasil;

        // Formula 5
        $formula5Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), collect(Arr::get($formulas, 'formula5'))->collapse()->toArray());
        });

        foreach (Arr::get($formulas, 'formula5') as $formula) {
            $valueA = isset($formula5Answers[$formula[0]]) ? ($formula5Answers[$formula[0]]['target_answer'] ?? $formula5Answers[$formula[0]]['achievement_answer']) : 0;
            $nma = isset($formula5Answers[$formula[1]]) ? json_decode($formula5Answers[$formula[1]]['target_answer'] ?? $formula5Answers[$formula[1]]['achievement_answer'], true)['NMA'] : 0;
            $nmd = isset($formula5Answers[$formula[1]]) ? json_decode($formula5Answers[$formula[1]]['target_answer'] ?? $formula5Answers[$formula[1]]['achievement_answer'], true)['NMD'] : 0;
            $valueB = $this->formula5B($nma, $nmd);

            $hasilKey = substr($formula[0], 0, -1) . 'HASIL';
            $hasil = $this->formula5(
                $valueA,
                $valueB
            );
            $results[$formula[0]] = $valueA;
            $results[$formula[1]] = $valueB;
            $results[$hasilKey] = $hasil;
        }

        // Formula 6
        $formula6Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula6'));
        });

        $ndtps = isset($formula6Answers['IBIK-STD-04.1']) ? json_decode($formula6Answers['IBIK-STD-04.1']['target_answer'] ?? $formula6Answers['IBIK-STD-04.1']['achievement_answer'], true)['NDTPS'] : 0;
        $hasilKey = substr('IBIK-STD-04.1-NDTPS', 0, -6) . 'HASIL';
        $hasil = $this->formula6($ndtps);
        $results['IBIK-STD-04.1'] = $hasil;

        // Formula 7
        $formula7Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula7'));
        });

        $nds3 = isset($formula7Answers['IBIK-STD-04.2']) ? json_decode($formula7Answers['IBIK-STD-04.2']['target_answer'] ?? $formula7Answers['IBIK-STD-04.2']['achievement_answer'], true)['NDS3'] : 0;
        $ndtps = isset($formula7Answers['IBIK-STD-04.2']) ? json_decode($formula7Answers['IBIK-STD-04.2']['target_answer'] ?? $formula7Answers['IBIK-STD-04.2']['achievement_answer'], true)['NDTPS'] : 0;
        $hasil = $this->formula7($nds3, $ndtps);
        $results['IBIK-STD-04.2'] = $hasil;

        // Formula 8
        $formula8Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula8'));
        });

        $ndgb = isset($formula8Answers['IBIK-STD-04.3']) ? json_decode($formula8Answers['IBIK-STD-04.3']['target_answer'] ?? $formula8Answers['IBIK-STD-04.3']['achievement_answer'], true)['NDGB'] : 0;
        $ndlk = isset($formula8Answers['IBIK-STD-04.3']) ? json_decode($formula8Answers['IBIK-STD-04.3']['target_answer'] ?? $formula8Answers['IBIK-STD-04.3']['achievement_answer'], true)['NDLK'] : 0;
        $ndl = isset($formula8Answers['IBIK-STD-04.3']) ? json_decode($formula8Answers['IBIK-STD-04.3']['target_answer'] ?? $formula8Answers['IBIK-STD-04.3']['achievement_answer'], true)['NDL'] : 0;
        $ndtps = isset($formula8Answers['IBIK-STD-04.3']) ? json_decode($formula8Answers['IBIK-STD-04.3']['target_answer'] ?? $formula8Answers['IBIK-STD-04.3']['achievement_answer'], true)['NDTPS'] : 0;
        $hasil = $this->formula8($ndgb, $ndlk, $ndl, $ndtps);
        $results['IBIK-STD-04.3'] = $hasil;

        // Formula 9
        $formula9Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula9'));
        });

        $nm = isset($formula9Answers['IBIK-STD-04.4']) ? json_decode($formula9Answers['IBIK-STD-04.4']['target_answer'] ?? $formula9Answers['IBIK-STD-04.4']['achievement_answer'], true)['NM'] : 0;
        $ndtps = isset($formula9Answers['IBIK-STD-04.4']) ? json_decode($formula9Answers['IBIK-STD-04.4']['target_answer'] ?? $formula9Answers['IBIK-STD-04.4']['achievement_answer'], true)['NDTPS'] : 0;
        $hasil = $this->formula9($nm, $ndtps);
        $results['IBIK-STD-04.4'] = $hasil;

        // Formula 10
        $formula10Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula10'));
        });

        $rdpu = isset($formula10Answers['IBIK-STD-04.5']) ? json_decode($formula10Answers['IBIK-STD-04.5']['target_answer'] ?? $formula10Answers['IBIK-STD-04.5']['achievement_answer'], true)['RDPU'] : 0;
        $hasil = $this->formula10($rdpu);
        $results['IBIK-STD-04.5'] = $hasil;

        // Formula 11
        $formula11Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula11'));
        });

        $rewmp = isset($formula11Answers['IBIK-STD-04.6']) ? json_decode($formula11Answers['IBIK-STD-04.6']['target_answer'] ?? $formula11Answers['IBIK-STD-04.6']['achievement_answer'], true)['REWMP'] : 0;
        $hasil = $this->formula11($rewmp);
        $results['IBIK-STD-04.6'] = $hasil;

        // Formula 12
        $formula12Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula12'));
        });

        $ndtt = isset($formula12Answers['IBIK-STD-04.7']) ? json_decode($formula12Answers['IBIK-STD-04.7']['target_answer'] ?? $formula12Answers['IBIK-STD-04.7']['achievement_answer'], true)['NDTT'] : 0;
        $ndt = isset($formula12Answers['IBIK-STD-04.7']) ? json_decode($formula12Answers['IBIK-STD-04.7']['target_answer'] ?? $formula12Answers['IBIK-STD-04.7']['achievement_answer'], true)['NDT'] : 0;
        $hasil = $this->formula12($ndtt, $ndt);
        $results['IBIK-STD-04.7'] = $hasil;

        // Formula 13
        $formula13Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula13'));
        });

        $nrd = isset($formula13Answers['IBIK-STD-04.8']) ? json_decode($formula13Answers['IBIK-STD-04.8']['target_answer'] ?? $formula13Answers['IBIK-STD-04.8']['achievement_answer'], true)['NRD'] : 0;
        $ndtps = isset($formula13Answers['IBIK-STD-04.8']) ? json_decode($formula13Answers['IBIK-STD-04.8']['target_answer'] ?? $formula13Answers['IBIK-STD-04.8']['achievement_answer'], true)['NDTPS'] : 0;
        $hasil = $this->formula13($nrd, $ndtps);
        $results['IBIK-STD-04.8'] = $hasil;

        // Formula 14
        $formula14Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), collect(Arr::get($formulas, 'formula14'))->collapse()->toArray());
        });


        foreach (Arr::get($formulas, 'formula14') as $formula) {
            $ni = isset($formula14Answers[$formula[0]]) ? json_decode($formula14Answers[$formula[0]]['target_answer'] ?? $formula14Answers[$formula[0]]['achievement_answer'], true)['NI'] : 0;
            $nn = isset($formula14Answers[$formula[0]]) ? json_decode($formula14Answers[$formula[0]]['target_answer'] ?? $formula14Answers[$formula[0]]['achievement_answer'], true)['NN'] : 0;
            $nl = isset($formula14Answers[$formula[0]]) ? json_decode($formula14Answers[$formula[0]]['target_answer'] ?? $formula14Answers[$formula[0]]['achievement_answer'], true)['NL'] : 0;
            $ntps = isset($formula14Answers[$formula[0]]) ? json_decode($formula14Answers[$formula[0]]['target_answer'] ?? $formula14Answers[$formula[0]]['achievement_answer'], true)['NTPS'] : 0;

            $hasil = $this->formula14($ni, $nn, $nl, $ntps);
            $hasilKey = $formula[0];
            $results [$hasilKey] = $hasil;
        }

        // Formula 15
        $formula15Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula15'));
        });

        $groupAnswer = [];
        foreach ($formula15Answers as $questionCode => $formula) {
            $basedAnswer = $formula['target_answer'] ?? $formula['achievement_answer'];
            $answer = json_decode($basedAnswer, true);
            if (is_array($answer)) {
                $groupAnswer = array_merge($groupAnswer, $answer);
            }
        }
        $results['IBIK-STD-04.11'] = $this->formula15($groupAnswer);

        // Formula 16
        $formula16Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula16'));
        });
        $nas = isset($formula16Answers['IBIK-STD-04.12']) ? json_decode($formula16Answers['IBIK-STD-04.12']['target_answer'] ?? $formula16Answers['IBIK-STD-04.12']['achievement_answer'], true)['NAS'] : 0;
        $ndtps = isset($formula16Answers['IBIK-STD-04.12']) ? json_decode($formula16Answers['IBIK-STD-04.12']['target_answer'] ?? $formula16Answers['IBIK-STD-04.12']['achievement_answer'], true)['NDTPS'] : 0;
        $results['IBIK-STD-04.12'] = $this->formula16($nas, $ndtps);

        // Formula 17
        $formula17Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula17'));
        });
        $na = isset($formula17Answers['IBIK-STD-04.13']) ? json_decode($formula17Answers['IBIK-STD-04.13']['target_answer'] ?? $formula17Answers['IBIK-STD-04.13']['achievement_answer'], true)['NA'] : 0;
        $nb = isset($formula17Answers['IBIK-STD-04.13']) ? json_decode($formula17Answers['IBIK-STD-04.13']['target_answer'] ?? $formula17Answers['IBIK-STD-04.13']['achievement_answer'], true)['NB'] : 0;
        $nc = isset($formula17Answers['IBIK-STD-04.13']) ? json_decode($formula17Answers['IBIK-STD-04.13']['target_answer'] ?? $formula17Answers['IBIK-STD-04.13']['achievement_answer'], true)['NC'] : 0;
        $nd = isset($formula17Answers['IBIK-STD-04.13']) ? json_decode($formula17Answers['IBIK-STD-04.13']['target_answer'] ?? $formula17Answers['IBIK-STD-04.13']['achievement_answer'], true)['ND'] : 0;
        $ndtps = isset($formula17Answers['IBIK-STD-04.13']) ? json_decode($formula17Answers['IBIK-STD-04.13']['target_answer'] ?? $formula17Answers['IBIK-STD-04.13']['achievement_answer'], true)['NDTPS'] : 0;

        $results['IBIK-STD-04.13'] = $this->formula17($na, $nb, $nc, $nd, $ndtps);

        // Formula 18
        $formula18Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula18'));
        });

        foreach ($formula18Answers as $formula) {
            $valueA = $formula['target_answer'] ?? $formula['achievement_answer'];

            $hasil41 = $results['IBIK-STD-04.1'] ?? 0;
            $hasil42 = $results['IBIK-STD-04.2'] ?? 0;
            $hasil43 = $results['IBIK-STD-04.3'] ?? 0;
            $hasil44 = $results['IBIK-STD-04.4'] ?? 0;
            $hasil45 = $results['IBIK-STD-04.5'] ?? 0;
            $hasil46 = $results['IBIK-STD-04.6'] ?? 0;
            $hasil47 = $results['IBIK-STD-04.7'] ?? 0;

            $results['IBIK-STD-04.14'] = $this->formula18($hasil41, $hasil42, $hasil43, $hasil44, $hasil45, $hasil46, $hasil47, $valueA);
        }

        // Formula 19
        $formula19Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), collect(Arr::get($formulas, 'formula19'))->collapse()->toArray());
        });

        foreach (Arr::get($formulas, 'formula19') as $formula) {
            $valueA = isset($formula19Answers[$formula[0]]) ? ($formula19Answers[$formula[0]]['target_answer'] ?? $formula19Answers[$formula[0]]['achievement_answer']) : 0;
            $valueB = isset($formula19Answers[$formula[1]]) ? ($formula19Answers[$formula[1]]['target_answer'] ?? $formula19Answers[$formula[1]]['achievement_answer']) : 0;
            $hasilKey = substr($formula[0], 0, -1) . 'HASIL';
            $hasil = $this->formula19(
                $valueA,
                $valueB
            );
            $results[$formula[0]] = $valueA;
            $results[$formula[1]] = $valueB;
            $results[$hasilKey] = $hasil;
        }

        // Formula 20
        $formula20Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula20'));
        });

        $dop = isset($formula20Answers['IBIK-STD-05.1']) ? json_decode($formula20Answers['IBIK-STD-05.1']['target_answer'] ?? $formula20Answers['IBIK-STD-05.1']['achievement_answer'], true)['DOP'] : 0;
        $hasil = $this->formula20($dop);
        $results['IBIK-STD-05.1'] = $hasil;

        // Formula 21
        $formula21Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula21'));
        });

        $dpd = isset($formula21Answers['IBIK-STD-05.2']) ? json_decode($formula21Answers['IBIK-STD-05.2']['target_answer'] ?? $formula21Answers['IBIK-STD-05.2']['achievement_answer'], true)['DPD'] : 0;
        $hasil = $this->formula21($dpd);
        $results['IBIK-STD-05.2'] = $hasil;

        // Formula 22
        $formula22Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), Arr::get($formulas, 'formula22'));
        });

        $dpkmd = isset($formula22Answers['IBIK-STD-05.3']) ? json_decode($formula22Answers['IBIK-STD-05.3']['target_answer'] ?? $formula22Answers['IBIK-STD-05.3']['achievement_answer'], true)['DPkMD'] : 0;
        $hasil = $this->formula22($dpkmd);
        $results['IBIK-STD-05.3'] = $hasil;

        // Formula 23
        $formula23Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula23'));
        });

        foreach ($formula23Answers as $formula) {
            $valueA = $formula['target_answer'] ?? $formula['achievement_answer'];

            // $hasil41 = $results['IBIK-STD-04.1'] ?? 0;
            // $hasil42 = $results['IBIK-STD-04.2'] ?? 0;
            // $hasil43 = $results['IBIK-STD-04.3'] ?? 0;
            // $hasil44 = $results['IBIK-STD-04.4'] ?? 0;
            // $hasil45 = $results['IBIK-STD-04.5'] ?? 0;
            // $hasil46 = $results['IBIK-STD-04.6'] ?? 0;
            // $hasil47 = $results['IBIK-STD-04.7'] ?? 0;
            // $hasil56 = $results['IBIK-STD-05.6'] ?? 0;

            // $results['IBIK-STD-05.4'] = $this->formula23($hasil41, $hasil42, $hasil43, $hasil44, $hasil45, $hasil46, $hasil47, $hasil56, $valueA);
            $results['IBIK-STD-05.4'] = $this->formula23($valueA);
        }

        // Formula 24
        $formula24Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), collect(Arr::get($formulas, 'formula24'))->collapse()->toArray());
        });

        foreach (Arr::get($formulas, 'formula24') as $formula) {
            $valueA = isset($formula24Answers[$formula[0]]) ? ($formula24Answers[$formula[0]]['target_answer'] ?? $formula24Answers[$formula[0]]['achievement_answer']) : 0;
            $valueB = isset($formula24Answers[$formula[1]]) ? ($formula24Answers[$formula[1]]['target_answer'] ?? $formula24Answers[$formula[1]]['achievement_answer']) : 0;
            $valueC = isset($formula24Answers[$formula[2]]) ? ($formula24Answers[$formula[2]]['target_answer'] ?? $formula24Answers[$formula[2]]['achievement_answer']) : 0;
            $hasilKey = substr($formula[0], 0, -1) . 'HASIL';
            $hasil = $this->formula24(
                $valueA,
                $valueB,
                $valueC
            );
            $results[$formula[0]] = $valueA;
            $results[$formula[1]] = $valueB;
            $results[$formula[2]] = $valueC;
            $results[$hasilKey] = $hasil;
        }

        // Formula 25
        $formula25Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return in_array($item->get('question_id'), collect(Arr::get($formulas, 'formula25'))->collapse()->toArray());
        });

        foreach (Arr::get($formulas, 'formula25') as $formula) {
            $valueA = isset($formula25Answers[$formula[0]]) ? ($formula25Answers[$formula[0]]['target_answer'] ?? $formula25Answers[$formula[0]]['achievement_answer']) : 0;
            $valueB = isset($formula25Answers[$formula[1]]) ? ($formula25Answers[$formula[1]]['target_answer'] ?? $formula25Answers[$formula[1]]['achievement_answer']) : 0;
            $valueC = isset($formula25Answers[$formula[2]]) ? ($formula25Answers[$formula[2]]['target_answer'] ?? $formula25Answers[$formula[2]]['achievement_answer']) : 0;
            $valueD = isset($formula25Answers[$formula[3]]) ? ($formula25Answers[$formula[3]]['target_answer'] ?? $formula25Answers[$formula[2]]['achievement_answer']) : 0;
            $valueE = isset($formula25Answers[$formula[4]]) ? ($formula25Answers[$formula[4]]['target_answer'] ?? $formula25Answers[$formula[2]]['achievement_answer']) : 0;
            $hasilKey = substr($formula[0], 0, -1) . 'HASIL';
            $hasil = $this->formula25($valueA, $valueB, $valueC, $valueD, $valueE);
            $results[$formula[0]] = $valueA;
            $results[$formula[1]] = $valueB;
            $results[$formula[2]] = $valueC;
            $results[$formula[3]] = $valueD;
            $results[$formula[4]] = $valueE;
            $results[$hasilKey] = $hasil;
        }

        // Formula 26
        $formula26Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula26'));
        });

        $jp = isset($formula26Answers['IBIK-STD-06.5']) ? json_decode($formula26Answers['IBIK-STD-06.5']['target_answer'] ?? $formula26Answers['IBIK-STD-06.5']['achievement_answer'], true)['JP'] : 0;
        $jb = isset($formula26Answers['IBIK-STD-06.5']) ? json_decode($formula26Answers['IBIK-STD-06.5']['target_answer'] ?? $formula26Answers['IBIK-STD-06.5']['achievement_answer'], true)['JB'] : 0;

        $results['IBIK-STD-06.5'] = $this->formula26($jp, $jb);

        // Formula 27
        $formula27Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula27'));
        });

        foreach ($formula27Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);
        }
        $results['IBIK-STD-06.8'] = $this->formula27($groupAnswer);

        // Formula 28
        $formula28Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), collect(Arr::get($formulas, 'formula28'))->collapse()->toArray());
        });

        $groupAnswer = [];
        foreach ($formula28Answers as $questionCode => $formula) {
            $basedAnswer = $formula['target_answer'] ?? $formula['achievement_answer'];
            $answer = json_decode($basedAnswer, true);
            if (is_array($answer)) {
                $groupAnswer = array_merge($groupAnswer, $answer);
            }
        }

        foreach (Arr::get($formulas, 'formula28') as $formula) {
            $valueA = $this->formula28A($groupAnswer);
            $valueB = isset($formula28Answers[$formula[1]]) ? ($formula28Answers[$formula[1]]['target_answer'] ?? $formula28Answers[$formula[1]]['achievement_answer']) : 0;
            $hasil = $this->formula28($valueA, $valueB);
            $hasilKey = substr($formula[0], 0, -1) . 'HASIL';
            $results[$formula[0]] = $valueA;
            $results[$formula[1]] = $valueB;
            $results[$hasilKey] = $hasil;
        }

        // Formula 29
        $formula29Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula29'));
        });

        foreach ($formula29Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);
        }
        $results['IBIK-STD-07.2'] = $this->formula29($groupAnswer);

        // Formula 30
        $formula30Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula30'));
        });

        foreach ($formula30Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);
        }
        $results['IBIK-STD-08.2'] = $this->formula30($groupAnswer);

        // Formula 31
        $formula31Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula31'));
        });

        foreach ($formula31Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);
        }
        $results['IBIK-STD-09.2'] = $this->formula31($groupAnswer);

        // Formula 32
        $formula32Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula32'));
        });

        foreach ($formula32Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);
        }
        $results['IBIK-STD-09.3'] = $this->formula32($groupAnswer);

        // Formula 33
        $formula33Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula33'));
        });

        foreach ($formula33Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);
        }
        $results['IBIK-STD-09.4'] = $this->formula33($groupAnswer);

        // Formula 34
        $formula34Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula34'));
        });

        foreach ($formula34Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);
        }
        $results['IBIK-STD-09.5'] = $this->formula34($groupAnswer);

        // Formula 35
        $formula35Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula35'));
        });

        foreach ($formula35Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);
        }
        $results['IBIK-STD-09.6'] = $this->formula35($groupAnswer);

        // Formula 36
        $formula36Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula36'));
        });

        foreach ($formula36Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);
        }
        $results['IBIK-STD-09.7'] = $this->formula36($groupAnswer);

        // Formula 37
        $formula37Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula37'));
        });


        foreach ($formula37Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);

        }
        $results['IBIK-STD-09.9'] = $this->formula37($groupAnswer);

        // Formula 38
        $formula38Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula38'));
        });

        foreach ($formula38Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);

        }
        $results['IBIK-STD-09.10'] = $this->formula38($groupAnswer);

        // Formula 39
        $formula39Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula39'));
        });

        foreach ($formula39Answers as $questionCode => $formula) {
            $groupAnswer = json_decode($formula['target_answer'] ?? $formula['achievement_answer'], true);

        }
        $results['IBIK-STD-09.11'] = $this->formula39($groupAnswer);


        // Formula 40
        $formula40Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula40'));
        });

        $groupAnswer = [];
        foreach ($formula40Answers as $questionCode => $formula) {
            $basedAnswer = $formula['target_answer'] ?? $formula['achievement_answer'];
            $answer = json_decode($basedAnswer, true);
            if (is_array($answer)) {
                $groupAnswer = array_merge($groupAnswer, $answer);
            }
        }
        $results['IBIK-STD-09.12'] = $this->formula40($groupAnswer);

        // Formula 41
        $formula41Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula41'));
        });

        $groupAnswer = [];
        foreach ($formula41Answers as $questionCode => $formula) {
            $varKey = substr($questionCode, strrpos($questionCode, '-') + 1);
            $groupAnswer[$varKey] = $formula['target_answer'] ?? $formula['achievement_answer'];
        }
        $results['IBIK-STD-09.13'] = $this->formula41($groupAnswer);

        // Formula 42
        $formula42Answers = collect($answers)->filter(function ($item) use ($formulas) {
            $item = collect($item);
            return Str::contains($item->get('question_id'), Arr::get($formulas, 'formula42'));
        });

        $groupAnswer = [];
        foreach ($formula42Answers as $questionCode => $formula) {
            $groupAnswer[] = $formula['target_answer'] ?? $formula['achievement_answer'];
        }
        $results['IBIK-STD-09.14'] = $this->formula42($groupAnswer);

        return $results;

    }

    public function formula1($values)
    {
        return $values;
    }

    public function formula2($value_a, $value_b)
    {
        return ($value_a + (2 * $value_b)) / 3;
    }

    public function formula3A($n1, $n2, $n3, $ndtps)
    {
            $a = 3;
            $b = 2;
            $c = 1;

            if ($ndtps > 0) {
                $RK = (($a * $n1) + ($b * $n2) + ($c * $n3)) / $ndtps;
            } else {
                $RK = 0;
            }

            return $RK >= 4 ? 4 : $RK;
    }

    public function formula3B($ni, $nn, $nw)
    {
        $a = 2;
        $b = 6;
        $c = 9;

        if ($ni >= $a) {
            $skorB = 4;
        } elseif ($ni < $a && $nn >= $b) {
            $skorB = 3 + ($ni / $a);
        } elseif (0 < $ni && $ni < $a && 0 < $nn && $nn < $b) {
            $skorB = 2 + (2 * ($ni / $a)) + ($nn / $b) - (($ni * $nn) / ($a * $b));
        } elseif ($ni == 0 && $nn == 0 && $nw >= $c) {
            $skorB = 2;
        } elseif ($ni == 0 && $nn == 0 && $nw < $c) {
            $skorB = (2 * $nw) / $c;
        }

        return $skorB;
    }

    public function formula4($jp, $jpl)
    {
        $JP = $jp ?? 0;
        $JPL = $jpl ?? 0;

        if ($JPL > 0) {
            $rasio = $JPL / $JP;
        } else {
            $rasio = 0;
        }

        if ($rasio < 5) {
            $skor = (4 * $rasio) / 5;
        } else {
            $skor = 4;
        }

        return $skor;
    }

    public function formula5B($nma, $nmd)
    {

        $NMA = $nma ?? 0;
        $NMD = $nmd ?? 0;

        if ($NMD > 0) {
            $PMA = $NMA / $NMD;
        } else {
            $PMA = 0;
        }

        if ($PMA < 1) {
            $skorB = 2 + (200 * $PMA);
        } else {
            $skorB = 4;
        }

        return $skorB;
    }

    public function formula5($value_a, $value_b)
    {
        return ((2 * $value_a) + $value_b) / 3;
    }

    public function formula6($ndtps)
    {
        $NDTPS = $ndtps ?? 0;
        if ($NDTPS < 3) {
            $skor = 0;
        } elseif (3 <= $NDTPS && $NDTPS < 12) {
            $skor = ((2 * $NDTPS) + 12) / 9;
        } elseif ($NDTPS >= 12) {
            $skor = 4;
        }

        return $skor;
    }

    public function formula7($nds3, $ndtps)
    {
        $NDS3 = $nds3 ?? 0;
        $NDTPS = $ndtps ?? 0;

        if ($NDTPS > 0) {
            $PDS3 = $NDS3 / $NDTPS * 100;
        } else {
            $PDS3 = 0;
        }


        if ($PDS3 < 50) {
            $skor = 2 + (4 * $PDS3) / 100;
        } elseif ($PDS3 >= 50) {
            $skor = 4;
        }

        return $skor;
    }

    public function formula8($ndgb, $ndlk, $ndl, $ndtps)
    {
        $NDGB = $ndgb ?? 0;
        $NDLK = $ndlk ?? 0;
        $NDL = $ndl ?? 0;
        $NDTPS = $ndtps ?? 0;

        if ($NDTPS > 0) {
            $PGBLKL = (($NDGB + $NDLK + $NDL) / $NDTPS) * 100;
        } else {
            $PGBLKL = 0;
        }

        if ($PGBLKL < 70) {
            $skor = 2 + ((20 * $PGBLKL) / 7) / 100;
        } elseif ($PGBLKL >= 70) {
            $skor = 4;
        }

        return $skor;
    }

    public function formula9($nm, $ndtps)
    {
        $NM = $nm ?? 0;
        $NDTPS = $ndtps ?? 0;

        if ($NDTPS > 0) {
            $RMD = $NM / $NDTPS;
        } else {
            $RMD = 0;
        }

        if ($RMD > 50) {
            $skor = 0;
        } elseif ($RMD < 25) {
            $skor = (4 * $RMD) / 25;
        } elseif (35 < $RMD && $RMD <= 50) {
            $skor = (200 - (4 * $RMD)) / 15;
        } elseif (25 <= $RMD && $RMD <= 35) {
            $skor = 4;
        }

        return $skor;
    }

    public function formula10($rdpu)
    {
        $RDPU = $rdpu ?? 0;
        if (6 < $RDPU && $RDPU <= 10) {
            $skor = 7 - ($RDPU / 2);
        } elseif ($RDPU <= 6) {
            $skor = 4;
        } elseif ($RDPU > 10) {
            $skor = 0;
        }

        return $skor;
    }

    public function formula11($rewmp)
    {
        $REWMP = $rewmp ?? 0;

        if ($REWMP < 6 || $REWMP > 18) {
            $skor = 0;
        } elseif (6 <= $REWMP && $REWMP < 12) {
            $skor = ((2 * $REWMP) - 12) / 3;
        } elseif (16 < $REWMP && $REWMP <= 18) {
            $skor = 36 - (2 * $REWMP);
        } elseif (12 <= $REWMP && $REWMP <= 16) {
            $skor = 4;
        }

        return $skor;
    }

    public function formula12($ndtt, $ndt)
    {
        $NDTT = $ndtt ?? 0;
        $NDT = $ndt ?? 0;

        if ($NDT > 0) {
            $PDTT = ($NDTT / ($NDT + $NDTT)) * 100;
        } else {
            $PDTT = 0;
        }


        if ($PDTT > 40) {
            $skor = 0;
        } elseif (10 < $PDTT && $PDTT <= 40) {
            $skor = (14 - (20 * ($PDTT / 100))) / 3;
        } elseif ($PDTT <= 10) {
            $skor = 4;
        }

        return $skor;
    }

    public function formula13($nrd, $ndtps)
    {
        $NRD = $nrd ?? 0;
        $NDTPS = $ndtps ?? 0;

        if ($NDTPS > 0) {
            $RRD = $NRD / $NDTPS;
        } else {
            $RRD = 0;
        }

        if ($RRD < 0.5) {
            $skor = 2 + (4 * $RRD);
        } elseif ($RRD >= 0.5) {
            $skor = 4;
        }

        return $skor;
    }

    public function formula14($ni, $nn, $nl, $ntps)
    {
        $a = 0.05;
        $b = 0.3;
        $c = 1;

        $NI = $ni ?? 0;
        $NN = $nn ?? 0;
        $NL = $nl ?? 0;
        $NDTPS = $ntps ?? 0;

        if ($NDTPS > 0) {
            $RI = $NI / 3 / $NDTPS;
            $RN = $NN / 3 / $NDTPS;
            $RL = $NL / 3 / $NDTPS;
        } else {
            $RI = 0;
            $RN = 0;
            $RL = 0;
        }


        if ($RI == 0 && $RN == 0 && $RL >= $c) {
            $skor = 2;
        } elseif ($RI == 0 && $RN == 0 && $RL < $c) {
            $skor = (2 * $RL) / $c;
        } elseif ($RI < $a && $RN >= $b) {
            $skor = 3 + ($RI / $a);
        } elseif ((0 < $RI && $RI < $a && 0 < $RN && $RN < $b) || (0 < $RN && $RN < $b && $RI == 0) || (0 < $RI && $RI < $a && $RN == 0)) {
            $skor = 2 + (2 * ($RI / $a)) + ($RN / $b) - (($RI * $RN) / ($a * $b));
        } elseif ($RI >= $a) {
            $skor = 4;
        } else {
            $skor = null;
        }

        return $skor;
    }

    public function formula15($values)
    {
        $a = 0.1;
        $b = 1;
        $c = 2;

        $NA1 = $values['NA1'] ?? 0;
        $NA2 = $values['NA2'] ?? 0;
        $NA3 = $values['NA3'] ?? 0;
        $NA4 = $values['NA4'] ?? 0;
        $NB1 = $values['NB1'] ?? 0;
        $NB2 = $values['NB2'] ?? 0;
        $NB3 = $values['NB3'] ?? 0;
        $NC1 = $values['NC1'] ?? 0;
        $NC2 = $values['NC2'] ?? 0;
        $NC3 = $values['NC3'] ?? 0;
        $NDTPS = $values['NDTPS'] ?? 0;

        if ($NDTPS > 0) {
            $RW = ($NA1 + $NB1 + $NC1) / $NDTPS;
            $RN = ($NA2 + $NA3 + $NB2 + $NC2) / $NDTPS;
            $RI = ($NA4 + $NB3 + $NC3) / $NDTPS;
        } else {
            $RW = 0;
            $RN = 0;
            $RI = 0;
        }

        if ($RI == 0 && $RN == 0 && $RW >= $c) {
            $skor = 2;
        } elseif ($RI == 0 && $RN == 0 && $RW < $c) {
            $skor = (2 * $RW) / $c;
        } elseif ($RI < $a && $RN >= $b) {
            $skor = 3 + ($RI / $a);
        } elseif ((0 < $RI && $RI < $a && 0 < $RN && $RN < $b) || (0 < $RN && $RN < $b && $RI == 0) || (0 < $RI && $RI < $a && $RN == 0)) {
            $skor = 2 + (2 * ($RI / $a)) + ($RN / $b) - (($RI * $RN) / ($a * $b));
        } elseif ($RI >= $a) {
            $skor = 4;
        } else {
            $skor = null;
        }

        return $skor;
    }

    public function formula16($nas, $ndtps)
    {
        $NAS = $nas ?? 0;
        $NDTPS = $ndtps ?? 0;

        if ($NDTPS > 0) {
            $RS = $NAS / $NDTPS;
        } else {
            $RS = 0;
        }
        if ($RS < 0.5) {
            $skor = 2 + (4 * $RS);
        } else {
            $skor = 4;
        }

        return $skor;
    }

    public function formula17($na, $nb, $nc, $nd, $ndtps)
    {
        $NA = $na ?? 0;
        $NB = $nb ?? 0;
        $NC = $nc ?? 0;
        $ND = $nd ?? 0;
        $NDTPS = $ndtps ?? 0;

        if ($NDTPS > 0) {
            $RLP = (2 * ($NA + $NB + $NC) + $ND) / $NDTPS;
        } else {
            $RLP = 0;
        }

        if ($RLP < 1) {
            $skor = 2 + (2 * $RLP);
        } else {
            $skor = 4;
        }

        return $skor;
    }

    public function formula18($a, $b, $c, $d, $e, $f, $g, $value_a)
    {
        $avg = ($a + $b + $c + $d + $e + $f + $g)/7;


        if ($avg >= 3.5) {
            return 4;
        } else {
            return $value_a;
        }
    }

    public function formula19($value_a, $value_b)
    {
        return ($value_a + $value_b) / 2;
    }

    public function formula20($values)
    {
        $DOP = $values ?? 0;

        if ($DOP < 20) {
            $skor = $DOP / 5;
        } elseif ($DOP >= 20) {
            $skor = 4;
        } else {
            $skor = null;
        }

        return $skor;
    }

    public function formula21($values)
    {
        $DPD = $values ?? 0;

        if ($DPD < 10) {
            $skor = (2 * $DPD) / 5;
        } elseif ($DPD >= 10) {
            $skor = 4;
        } else {
            $skor = null;
        }

        return $skor;
    }

    public function formula22($values)
    {
        $DPkMD = $values ?? 0;

        if ($DPkMD < 5) {
            $skor = (4 * $DPkMD) / 5;
        } elseif ($DPkMD >= 5) {
            $skor = 4;
        } else {
            $skor = null;
        }

        return $skor;
    }

    public function formula23($values)
    {
        // $a, $b, $c, $d, $e, $f, $g, $h, $value_a
        return $values;
        // $avg = ($a + $b + $c + $d + $e + $f + $g + $h)/8;

        // if ($avg >= 3.5) {
        //     return 4;
        // } else {
        //     return $value_a;
        // }
    }

    public function formula24($value_a, $value_b, $value_c)
    {
        return ($value_a + (2 * $value_b) + (2 * $value_c)) / 5;
    }

    public function formula25($value_a, $value_b, $value_c, $value_d, $value_e)
    {
        return ($value_a + (2 * $value_b) + (2 * $value_c) + (2 * $value_d) + (2 * $value_e)) / 9;
    }

    public function formula26($jp, $jb)
    {
        $JP = $jp ?? 0;
        $JB = $jb ?? 0;

        if ($JB > 0) {
            $PJP = ($JP / $JB) * 100;
        } else {
            $PJP = 0;
        }

        if ($PJP < 20) {
            $skor = (20 * $PJP) / 100;
        } elseif ($PJP >= 20) {
            $skor = 4;
        }

        return $skor;
    }

    public function formula27($values)
    {
        $NMKI = $values ?? 0;

        if ($NMKI == 1) {
            $skor = 2;
        } elseif (($NMKI == 2) || ($NMKI == 3)) {
            $skor = 3;
        } elseif ($NMKI > 3) {
            $skor = 4;
        } else {
            return null;
        }

        return $skor;
    }

    public function formula28A($values)
    {
        // return $values;
        // Mencari nilai A
        $tkms = [];

        for ($i = 1; $i <= 5; $i++) {
            $ai = isset($values["TKM$i-ai_$i"]) ? (int) $values["TKM$i-ai_$i"] : 0;
            $bi = isset($values["TKM$i-bi_$i"]) ? (int) $values["TKM$i-bi_$i"] : 0;
            $ci = isset($values["TKM$i-ci_$i"]) ? (int) $values["TKM$i-ci_$i"] : 0;
            $di = isset($values["TKM$i-di_$i"]) ? (int) $values["TKM$i-di_$i"] : 0;

            $tkm_i = ((4 * $ai) + (3 * $bi) + (2 * $ci) + $di) / 4;
            $tkms[] = $tkm_i;
        }
        $tkm = array_sum($tkms) / 5;

        if ($tkm < 25) {
            $skorA = 0;
        } elseif (25 < $tkm && $tkm < 75) {
            $skorA = (8 * ($tkm/100)) - 2;
        } elseif ($tkm >= 75) {
            $skorA = 4;
        }

        return $skorA;
    }

    public function formula28($value_a, $value_b)
    {
        return ($value_a + (2 * $value_b)) / 3;
    }

    public function formula29($values)
    {
        $NPM = $values['NPM'] ?? 0;
        $NPD = $values['NPD'] ?? 0;

        if ($NPD > 0) {
            $PPDM = ($NPM / $NPD) * 100;
        } else {
            $PPDM = 0;
        }

        if ($PPDM < 25) {
            $skor = 2 + (8 * $PPDM) / 100;
        } elseif ($PPDM >= 25) {
            $skor = 4;
        } else {
            return null;
        }
        return $skor;
    }

    public function formula30($values)
    {
        $NPkMM = $values['NPkMM'] ?? 0;
        $NPkMD = $values['NPkMD'] ?? 0;

        if ($NPkMD > 0) {
            $PPkMDM = ($NPkMM / $NPkMD) * 100;
        } else {
            $PPkMDM = 0;
        }

        if ($PPkMDM < 25) {
            $skor = 2 + (8 * $PPkMDM) / 100;
        } elseif ($PPkMDM >= 25) {
            $skor = 4;
        } else {
            return null;
        }
        return $skor;
    }

    public function formula31($values)
    {
        $RIPK = $values['RIPK'] ?? 0;

        if (2 <= $RIPK && $RIPK < 3.25) {
            $skor = ((8 * $RIPK) - 6) / 5;
        } elseif ($RIPK >= 3.25) {
            $skor = 4;
        } else {
            return null;
        }

        return $skor;
    }

    public function formula32($values)
    {
        $NI = $values['NI'] ?? 0;
        $NN = $values['NN'] ?? 0;
        $NW = $values['NW'] ?? 0;
        $NM = $values['NM'] ?? 0;

        if ($NM > 0) {
            $RI = $NI / $NM * 100;
            $RN = $NN / $NM * 100;
            $RW = $NW / $NM * 100;
        } else {
            $RI = 0;
            $RN = 0;
            $RW = 0;
        }

        $a = 0.1;
        $b = 1;
        $c = 2;

        if ($RI == 0 && $RN == 0 && $RW >= $c) {
            $skor = 2;
        } elseif ($RI == 0 && $RN == 0 && $RW < $c) {
            $skor = (2 * $RW) / $c;
        } elseif ($RI < $a && $RN >= $b) {
            $skor = 3 + ($RI / $a);
        } elseif ((0 < $RI && $RI < $a && 0 < $RN && $RN < $b) || (0 < $RN && $RN < $b && $RI == 0) || (0 < $RI && $RI < $a && $RN == 0)) {
            $skor = 2 + (2 * ($RI / $a)) + ($RN / $b) - (($RI * $RN) / ($a * $b));
        } elseif ($RI >= $a) {
            $skor = 4;
        } else {
            $skor = null;
        }

        return $skor;
    }

    public function formula33($values)
    {
        $NI = $values['NI'] ?? 0;
        $NN = $values['NN'] ?? 0;
        $NW = $values['NW'] ?? 0;
        $NM = $values['NM'] ?? 0;

        if ($NM > 0) {
            $RI = $NI / $NM * 100;
            $RN = $NN / $NM * 100;
            $RW = $NW / $NM * 100;
        } else {
            $RI = 0;
            $RN = 0;
            $RW = 0;
        }

        $a = 0.2;
        $b = 2;
        $c = 4;

        if ($RI == 0 && $RN == 0 && $RW >= $c) {
            $skor = 2;
        } elseif ($RI == 0 && $RN == 0 && $RW < $c) {
            $skor = (2 * $RW) / $c;
        } elseif ($RI < $a && $RN >= $b) {
            $skor = 3 + ($RI / $a);
        } elseif ((0 < $RI && $RI < $a && 0 < $RN && $RN < $b) || (0 < $RN && $RN < $b && $RI == 0) || (0 < $RI && $RI < $a && $RN == 0)) {
            $skor = 2 + (2 * ($RI / $a)) + ($RN / $b) - (($RI * $RN) / ($a * $b));
        } elseif ($RI >= $a) {
            $skor = 4;
        } else {
            $skor = null;
        }

        return $skor;
    }

    public function formula34($values)
    {
        $MS = $values['MS'] ?? 0;

        if ($MS <= 3) {
            $skor = 0;
        } elseif (3 < $MS && $MS <= 3.5) {
            $skor = (8 * $MS) - 24;
        } elseif (4.5 < $MS && $MS <= 7) {
            $skor = (56 - (8 * $MS)) / 5;
        } elseif (3.5 < $MS && $MS <= 4.5) {
            $skor = 4;
        } else {
            return null;
        }

        return $skor;
    }

    public function formula35($values)
    {
        $PTW = $values['PTW'] ?? 0;

        if ($PTW < 50) {
            $skor = 1 + (6 * $PTW);
        } elseif ($PTW >= 50) {
            $skor = 4;
        } else {
            return null;
        }

        return $skor;
    }

    public function formula36($values)
    {
        $PPS = $values['PPS'] ?? 0;

        if (30 < $PPS & $PPS < 85) {
            $skor = ((80 * $PPS) - 24) / 11;
        } elseif ($PPS >= 85) {
            $skor = 4;
        } else {
            return null;
        }

        return $skor;
    }

    public function formula37($values)
    {
        $WT = $values['WT'] ?? 0;

        if ($WT > 18) {
            $skor = 0;
        } elseif (6 <= $WT && $WT <= 18) {
            $skor = (18 - $WT) / 3;
        } elseif ($WT < 6) {
            $skor = 4;
        } else {
            return null;
        }

        return $skor;
    }

    public function formula38($values)
    {
        $PBS = $values['PBS'] ?? 0;

        if ($PBS < 60) {
            $skor = (20 * $PBS) / 3;
        } elseif ($PBS >= 60) {
            $skor = 4;
        } else {
            return null;
        }

        return $skor;
    }

    public function formula39($values)
    {
        $NI = $values['NI'] ?? 0;
        $NN = $values['NN'] ?? 0;
        $NW = $values['NW'] ?? 0;
        $NL = $values['NL'] ?? 0;
        $NJ = $values['NJ'] ?? 0;

        if ($NJ > 0) {
            $RI = ($NI / $NJ) * 100;
            $RN = ($NN / $NJ) * 100;
            $RW = ($NW / $NJ) * 100;
        } else {
            $RI = 0;
            $RN = 0;
            $RW = 0;
        }

        $a = 5;
        $b = 20;
        $c = 90;

        if ($RI == 0 && $RN == 0 && $RW >= $c) {
            $skor = 2;
        } elseif ($RI == 0 && $RN == 0 && $RW < $c) {
            $skor = (2 * $RW) / $c;
        } elseif ($RI < $a && $RN >= $b) {
            $skor = 3 + ($RI / $a);
        } elseif ((0 < $RI && $RI < $a && 0 < $RN && $RN < $b) || (0 < $RN && $RN < $b && $RI == 0) || (0 < $RI && $RI < $a && $RN == 0)) {
            $skor = 2 + (2 * ($RI / $a)) + ($RN / $b) - (($RI * $RN) / ($a * $b));
        } elseif ($RI >= $a) {
            $skor = 4;
        } else {
            $skor = null;
        }

        if ($NL > 0) {
            $PJ = ($NJ / $NL) * 100;
        } else {
            $PJ = 0;
        }

        if ($NL >= 300) {
            $Prmin = 30;
        } elseif ($NL < 300) {
            $Prmin = 50 - (($NL / 300) * 20);
        }

        if ($PJ >= $Prmin) {
            $skorAkhir = $skor;
        } elseif ($PJ < $Prmin) {
            $skorAkhir = ($PJ / $Prmin) * $skor;
        } else {
            $skorAkhir = null;
        }

        return $skorAkhir;
    }

    public function formula40($values)
    {
        $NJ = $values['NJ'] ?? 0;
        $NL = $values['NL'] ?? 0;

        $tkms = [];

        for ($i = 1; $i <= 7; $i++) {
            $ai = isset($values["TK$i-ai_$i"]) ? (int) $values["TK$i-ai_$i"] : 0;
            $bi = isset($values["TK$i-bi_$i"]) ? (int) $values["TK$i-bi_$i"] : 0;
            $ci = isset($values["TK$i-ci_$i"]) ? (int) $values["TK$i-ci_$i"] : 0;
            $di = isset($values["TK$i-di_$i"]) ? (int) $values["TK$i-di_$i"] : 0;

            $tkm_i = ((4 * $ai) + (3 * $bi) + (2 * $ci) + $di) / 100;
            $tkms[] = $tkm_i;
        }
        $skor = array_sum($tkms) / 7;

        if ($NL > 0) {
            $PJ = ($NJ / $NL) * 100;
        } else {
            $PJ = 0;
        }

        if ($NL >= 300) {
            $Prmin = 30;
        } elseif ($NL < 300) {
            $Prmin = 50 - (($NL / 300) * 20);
        }

        if ($PJ >= $Prmin) {
            $skorAkhir = $skor;
        } elseif ($PJ < $Prmin) {
            $skorAkhir = ($PJ / $Prmin) * $skor;
        }

        return $skorAkhir;
    }

    public function formula41($values)
    {
        $NA1 = $values['NA1'] ?? 0;
        $NA2 = $values['NA2'] ?? 0;
        $NA3 = $values['NA3'] ?? 0;
        $NA4 = $values['NA4'] ?? 0;
        $NB1 = $values['NB1'] ?? 0;
        $NB2 = $values['NB2'] ?? 0;
        $NB3 = $values['NB3'] ?? 0;
        $NC1 = $values['NC1'] ?? 0;
        $NC2 = $values['NC2'] ?? 0;
        $NC3 = $values['NC3'] ?? 0;
        $NM = $values['NM'] ?? 0;

        if ($NM > 0) {
            $RL = (($NA1 + $NB1 + $NC1) / $NM) * 100;
            $RN = (($NA2 + $NA3 + $NB2 + $NC2) / $NM) * 100;
            $RI = (($NA4 + $NB3 + $NC3) / $NM) * 100;
        } else {
            $RL = 0;
            $RN = 0;
            $RI = 0;
        }

        $a = 1;
        $b = 10;
        $c = 50;

        if ($RI == 0 && $RN == 0 && $RL >= $c) {
            $skor = 2;
        } elseif ($RI == 0 && $RN == 0 && $RL < $c) {
            $skor = (2 * $RL) / $c;
        } elseif ($RI < $a && $RN >= $b) {
            $skor = 3 + ($RI / $a);
        } elseif ((0 < $RI && $RI < $a && 0 < $RN && $RN < $b) || (0 < $RN && $RN < $b && $RI == 0) || (0 < $RI && $RI < $a && $RN == 0)) {
            $skor = 2 + (2 * ($RI / $a)) + ($RN / $b) - (($RI * $RN) / ($a * $b));
        } elseif ($RI >= $a) {
            $skor = 4;
        } else {
            $skor = null;
        }

        return $skor;
    }

    public function formula42($values)
    {
        $NA = $values['NA'] ?? 0;
        $NB = $values['NB'] ?? 0;
        $NC = $values['NC'] ?? 0;
        $ND = $values['ND'] ?? 0;

        $NLP = 2 * ($NA + $NB + $NC) + $ND;

        if ($NLP < 1) {
            $skor = 2 + (2 * $NLP);
        } elseif ($NLP >= 1) {
            $skor = 4;
        } else {
            return null;
        }

        return $skor;
    }
}
