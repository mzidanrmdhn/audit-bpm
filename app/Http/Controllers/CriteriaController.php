<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Utils\Permission;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    protected $permission = [
        "view" => "can_view_criteria",
        "store" => "can_store_criteria",
        "delete" => "can_delete_criteria",
    ];

    public function index()
    {
        (new Permission($this->permission ?? null))->can("view");
        $criteria = Criteria::all();
        return view('criteria.index')->with('data', $criteria);
    }

    public function store(Request $request, $id = null)
    {
        (new Permission($this->permission ?? null))->can("store");
        Criteria::updateOrCreate(
            [
                'id' => $id
            ],
            [
                'name' => $request->name
            ]
        );

        return redirect()->back();
    }

    public function destroy($id)
    {
        (new Permission($this->permission ?? null))->can("delete");
        $criteria = Criteria::findOrFail($id);
        $criteria->delete();

        return redirect()->route('criteria.index');
    }
}
