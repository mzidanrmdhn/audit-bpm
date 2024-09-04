<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Services\GraphService;
use App\Services\ScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GraphController extends Controller
{
    protected $questionsHelper;
    protected $graphService;
    protected $scoreService;

    public function __construct(GraphService $graphService, ScoreService $scoreService)
    {
        $this->graphService = $graphService;
        $this->scoreService = $scoreService;
    }

    public function index(Request $request)
    {
        $unit_id = $request->input('unit_id', Auth::user()->unit_id);
        $data = $this->graphService->getTableData($unit_id);

        return view('results.graph')->with($data);
    }

    public function getChartData(Request $request)
    {
        $unit_id = $request->input('unit_id', Auth::user()->unit_id);
        $data = $this->graphService->getChartData($unit_id);

        return response()->json($data);
    }

    public function generatePdf(Request $request)
    {
        $unit_id = $request->input('unit_id', Auth::user()->unit_id);

        $graphData = $this->graphService->getTableData($unit_id);
        $chartData = $this->graphService->getChartData($unit_id);
        $scoreData = $this->scoreService->getScoreData($unit_id, Auth::user());

        $unitName = Unit::find($unit_id)->name;

        $data = array_merge($graphData, $chartData, $scoreData);

        return view('reports.print', $data);
    }
}
