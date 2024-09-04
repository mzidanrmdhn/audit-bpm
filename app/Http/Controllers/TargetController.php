<?php

namespace App\Http\Controllers;

use App\Helpers\QuestionsHelper;
use App\Models\Achievement;
use App\Models\Criteria;
use App\Models\Questions;
use App\Models\Target;
use App\Utils\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TargetController extends Controller
{
    private $questionsHelper;

    protected $permission = [
        "view" => "can_view_target",
        "save" => "can_save_target"
    ];

    public function __construct()
    {

        $this->questionsHelper = new QuestionsHelper();
    }

    public function index(Request $request)
    {
        (new Permission($this->permission ?? null))->can("view");
        $index = $request->query('index', 0);

        $questions = Questions::with('inputs', 'choices', 'weights', 'subCriteria')->get();
        $groupedQuestion = $questions->groupBy(function ($question) {
            return $question->subCriteria->criteria->name;
        });

        $criteriaKeys = Criteria::all();
        $currentCriteria = $criteriaKeys[$index];

        $answers = Achievement::where('unit_id', Auth::user()->unit_id)->get()->keyBy('question_id');
        $target = Target::where('unit_id', Auth::user()->unit_id)->get()->keyBy('question_id');

        $parsedAnswers = [];
        foreach ($target as $answer) {
            $parsedAnswers[$answer->question_id] = json_decode($answer->target_answer, true);
        }

        return view('question.target', [
            'questions' => $groupedQuestion[$currentCriteria->name],
            'currentCriteria' => $currentCriteria,
            'parsedAnswers' => $parsedAnswers,
            'criteriaKeys' => $criteriaKeys,
            'currentIndex' => $index,
            'answers' => $answers,
            'target' => $target,
        ]);
    }

    public function save(Request $request)
    {
        (new Permission($this->permission ?? null))->can("save");
        $data = $request->all();

        if (isset($data['answers'])) {
            foreach ($data['answers'] as $questionId => $answer) {
                if (is_array($answer)) {
                    foreach ($answer as $subKey => $subAnswer) {
                        if ($subAnswer !== null) {
                            Target::updateOrCreate(
                                [
                                    'unit_id' => 1,
                                    'question_id' => $questionId . '-' . $subKey
                                ],
                                [
                                    'target_answer' => $subAnswer
                                ]
                            );
                        }
                    }
                } else {
                    if ($answer !== null) {
                        Target::updateOrCreate(
                            [
                                'unit_id' => 1,
                                'question_id' => $questionId
                            ],
                            [
                                'target_answer' => $answer
                            ]
                        );
                    }
                }
            }
        }

        $currentIndex = $request->input('currentIndex');
        $criteriaCount = count(collect($this->questionsHelper->flattenQuestions())->groupBy('criteria')->keys()->all());

        // Jika currentIndex adalah halaman terakhir, tetap di halaman yang sama
        if ($currentIndex < $criteriaCount - 1) {
            $nextIndex = $currentIndex + 1;
            return redirect()->route('target.index', ['index' => $nextIndex]);
        } else {
            return redirect()->route('target.index', ['index' => $currentIndex])->with('success', 'Data berhasil disimpan!');
        }
    }
}
