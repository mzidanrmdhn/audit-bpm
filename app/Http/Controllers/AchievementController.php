<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Target;
use App\Utils\Permission;
use App\Models\Achievement;
=======
use App\Helpers\QuestionsHelper;
use App\Models\Achievement;
use App\Models\Criteria;
use App\Models\Questions;
use App\Models\Target;
use App\Models\SubCriteria;
use App\Utils\Permission;
>>>>>>> upstream/main
use Illuminate\Http\Request;
use App\Helpers\QuestionsHelper;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
{
    private $questionsHelper;

    protected $permission = [
        "view" => "can_view_achievement",
        "save" => "can_save_achievement"
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

<<<<<<< HEAD
        $answers = Achievement::where('unit_id', 1)->get()->keyBy('question_id');
        $target = Target::where('unit_id', 1)->get()->keyBy('question_id');
=======
        $criteriaKeys = Criteria::all();
        $currentCriteria = $criteriaKeys[$index];

        $answers = Achievement::where('unit_id', Auth::user()->unit_id)->get()->keyBy('question_id');
        $target = Target::where('unit_id', Auth::user()->unit_id)->get()->keyBy('question_id');
>>>>>>> upstream/main

        $parsedAnswers = [];
        foreach ($answers as $answer) {
            $parsedAnswers[$answer->question_id] = json_decode($answer->achievement_answer, true);
        }

        return view('question.achievement', [
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
        $userId = 1;

        if (isset($data['answers'])) {
            foreach ($data['answers'] as $questionId => $answer) {
                if (is_array($answer)) {
<<<<<<< HEAD
                    foreach ($answer as $subKey => $subAnswer) {
                        if ($subAnswer !== null) {
                            Achievement::updateOrCreate(
                                [
                                    'unit_id' => 1,
                                    'question_id' => $questionId . '-' . $subKey
                                ],
                                [
                                    'achieve_answer' => $subAnswer
                                ]
                            );
=======
                    $jsonAnswer = [];

                    foreach ($answer as $label => $value) {
                        if ($value !== null) {
                            $jsonAnswer[$label] = $value;
>>>>>>> upstream/main
                        }
                    }

                    if (!empty($jsonAnswer)) {
                        Achievement::updateOrCreate(
                            [
                                'unit_id' => Auth::user()->unit_id,
                                'question_id' => $questionId
                            ],
                            [
                                'achievement_answer' => json_encode($jsonAnswer, JSON_FORCE_OBJECT)
                            ]
                        );
                    }
                } else {
                    if ($answer !== null) {
                        Achievement::updateOrCreate(
                            [
                                'unit_id' => 1,
                                'question_id' => $questionId
                            ],
                            [
                                'achievement_answer' => $answer
                            ]
                        );
                    }
                }
            }
        }

        $currentIndex = $request->input('currentIndex');
        $criteriaCount = count(collect($this->questionsHelper->flattenQuestions())->groupBy('criteria')->keys()->all());

        if ($currentIndex < $criteriaCount - 1) {
            $nextIndex = $currentIndex + 1;
            return redirect()->route('achievement.index', ['index' => $nextIndex]);
        } else {
            return redirect()->route('achievement.index', ['index' => $currentIndex])->with('success', 'Data berhasil disimpan');
        }
    }
}
