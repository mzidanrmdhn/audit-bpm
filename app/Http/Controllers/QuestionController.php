<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\QuestionsHelper;
use App\Models\Criteria;
use App\Models\QuestionChoices;
use App\Models\QuestionInputs;
use App\Models\Questions;
use App\Models\SubCriteria;

class QuestionController extends Controller
{
    protected $question;

    public function __construct()
    {
        $this->question = new QuestionsHelper;
    }

    public function index()
    {
        $questions = Questions::with(['subCriteria', 'subCriteria.criteria', 'choices', 'inputs'])->get();
        return view('management-question.index')->with('data', $questions);
    }

    public function create()
    {
        return view('management-question.edit');
    }

    public function edit($id = null)
    {
        $question = $id ? Questions::with('choices', 'inputs', 'subCriteria', 'subCriteria.criteria')->findOrFail($id) : new Questions();
        $criteria = Criteria::all();
        $subCriteria = SubCriteria::all()->groupBy('criteria_id');
        return view('management-question.edit', [
            'question' => $question,
            'criteria' => $criteria,
            'subCriteria' => $subCriteria
        ]);
    }

    public function store(Request $request, $id = null)
    {
        $question = Questions::updateOrCreate(
            [
                'id' => $id,
                'code' => $request->code
            ],
            [
                'main_question' => $request->main_question,
                'type' => $request->type,
                'sub_criteria_id' => $request->sub_criteria,
            ]
        );

        $question->choices()->delete();
        $question->inputs()->delete();

        if ($question->type === 'option') {
            if (!empty($request->choices)) {
                foreach ($request->choices as $choice) {
                    if (isset($choice['value']) && isset($choice['description'])) {
                        $question->choices()->create($choice);
                    }
                }
            }
        } elseif ($question->type === 'input') {
            // Simpan input jika tipe adalah 'input'
            if (!empty($request->inputs)) {
                foreach ($request->inputs as $input) {
                    if (isset($input['label']) && isset($input['input_question'])) {
                        $question->inputs()->create($input);
                    }
                }
            }
        }

        return redirect()->route('questions.index');
    }

    public function destroy($id)
    {
        // (new Permission($this->permission ?? null))->can("delete");
        $unit = Questions::findOrFail($id);
        $unit->delete();

        return redirect()->route('questions.index');
    }

    public function storeDumpData()
    {
        $flattenedQuestions = $this->question->flattenQuestions();

        foreach ($flattenedQuestions as $questionData) {
            $criteria = Criteria::firstOrCreate(['name' => $questionData['criteria']]);

            $subCriteria = SubCriteria::firstOrCreate([
                'name' => $questionData['subCriteria'],
                'criteria_id' => $criteria->id
            ]);

            $question = Questions::updateOrCreate([
                'code' => $questionData['code'],
            ],
            [
                'sub_criteria_id' => $subCriteria->id,
                'type' => $questionData['type'],
                'main_question' => $questionData['question']['main'] ?? $questionData['question'],
            ]);

            if ($questionData['type'] === 'option' && isset($questionData['choices'])) {
                foreach ($questionData['choices'] as $value => $description) {
                    QuestionChoices::updateOrCreate([
                        'question_id' => $question->id,
                        'value' => $value,
                    ],
                    [
                        'description' => $description,
                    ]);
                }
            }

            if ($questionData['type'] === 'input' && isset($questionData['question'])) {
                foreach ($questionData['question'] as $label => $details) {
                    if ($label !== 'main') { // Skip main question
                        // Cek apakah $details adalah array untuk menghindari error
                        if (is_array($details)) {
                            foreach ($details as $key => $value) {
                                // Gabungkan label dan key untuk keunikan, gunakan ternary operator
                                $uniqueLabel = $key !== null ? $label . '-' . $key : $label;

                                // Simpan detail
                                QuestionInputs::updateOrCreate(
                                    [
                                        'question_id' => $question->id,
                                        'label' => $uniqueLabel,
                                    ],
                                    [
                                        'input_question' => $value,
                                    ]
                                );
                            }
                        } else {
                            // Jika $details bukan array, simpan sebagai satu entri
                            QuestionInputs::updateOrCreate(
                                [
                                    'question_id' => $question->id,
                                    'label' => $label,
                                ],
                                [
                                    'input_question' => $details,
                                ]
                            );
                        }
                    }
                }
            }
        }

        return response()->json(['message' => 'Data saved successfully']);
    }
}
