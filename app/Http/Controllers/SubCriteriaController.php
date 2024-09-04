<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Utils\Permission;
use App\Models\SubCriteria;
use Illuminate\Http\Request;

class SubCriteriaController extends Controller
{
    protected $permission = [
        "view" => "can_view_sub_criteria",
        "store" => "can_store_sub_criteria",
        "delete" => "can_delete_sub_criteria",
    ];

    public function index()
    {
        (new Permission($this->permission ?? null))->can("view");
        $criteria = Criteria::all();
    $subCriteria = SubCriteria::with('criteria')->get();
        return view('sub-criteria.index')->with([
            'data' => $subCriteria,
            'criterias' => $criteria
        ]);
    }

    public function store(Request $request, $id = null)
    {
        (new Permission($this->permission ?? null))->can("store");
        SubCriteria::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'criteria_id' => $request->criteria_id,
                'name' => $request->name,
            ]
        );

        return redirect()->back();
    }

    public function destroy($id)
    {
        (new Permission($this->permission ?? null))->can("delete");
        $subCriteria = SubCriteria::findOrFail($id);
        $subCriteria->delete();

        return redirect()->route('sub-criteria.index');
    }
}
