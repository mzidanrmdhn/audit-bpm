<?php

namespace App\Http\Controllers;

use App\Helpers\QuestionsHelper;
use App\Models\Questions;
use App\Models\Weight;
use Illuminate\Http\Request;

class WeightController extends Controller
{
    protected $question;

    public function __construct()
    {
        $this->question = new QuestionsHelper;
    }

    public function index()
    {
        $question = Questions::with('weights')->get();
        $weight = Weight::with('question')->get();
        return view('question-weight.index')->with([
            'data' => $weight,
            'questions' => $question,
        ]);
    }

    public function store(Request $request, $id = null)
    {
        Weight::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'question_id' => $request->question_id,
                'weight' => $request->weight,
            ]
        );

        return redirect()->back();
    }

    public function destroy($id)
    {
        $subCriteria = Weight::findOrFail($id);
        $subCriteria->delete();

        return redirect()->route('question-weight.index');
    }

    public function storeDataDump()
    {
        $datas = $this->question->getAllWeights();

        foreach ($datas as $code => $data) {
            $question = Questions::where('code', $code)->first();

            if ($question) {
                Weight::firstOrCreate([
                    'question_id' => $question->id,
                    'weight' => $data
                ]);
            }
        }

        return 'Data berhasil disimpan';
    }
}
