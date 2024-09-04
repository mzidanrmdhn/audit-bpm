<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Utils\Permission;
use Illuminate\Http\Request;
use App\Models\ManagementUnit;

class ManagementUnitController extends Controller
{
    protected $permission = [
        "view" => "can_view_management_unit",
        "store" => "can_store_management_unit",
        "delete" => "can_delete_management_unit",
    ];

    public function index()
    {
        (new Permission($this->permission ?? null))->can("view");
        $data = Unit::all();
        return view('management-unit.index', ['data' => $data]);
    }

    public function store(Request $request, $id = null)
    {
        (new Permission($this->permission ?? null))->can("store");
        Unit::updateOrCreate(
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
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return redirect()->route('management-unit.index');
    }
}
