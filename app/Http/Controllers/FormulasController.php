<?php

namespace App\Http\Controllers;

use App\Services\ScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormulasController extends Controller
{

    public function index(Request $request, ScoreService $scoreService)
    {
        $unit_id = $request->input('unit_id', Auth::user()->unit_id);
        $data = $scoreService->getScoreData($unit_id, Auth::user());
        $accreditation = $scoreService->generateWSM($unit_id);

        return view('results.score')->with($data, $accreditation);
    }

    public function generate(ScoreService $scoreService)
    {
        $unit_id = Auth::user()->unit_id;
        $scoreService->generate($unit_id);
        // return $scoreService->accreditation($unit_id);
        // $results['predAccre'] = $scoreService->predAccreditation($unit_id);
        // $results['score'] = $scoreService->weightedScore($unit_id);
        // $results['generateWSM'] = $scoreService->generateWSM();

        // return $results;

        return redirect('/skor');
    }
}
