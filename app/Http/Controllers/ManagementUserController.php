<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Utils\Permission;
use Illuminate\Http\Request;
use App\Services\ManagementUserService;
use Diatria\LaravelInstant\Models\Role;
use Diatria\LaravelInstant\Traits\InstantControllerTrait;

class ManagementUserController extends Controller
{
    use InstantControllerTrait;

    protected $service, $model;
    protected $permission = [
        "create" => "can_create_users",
        "view" => "can_view_users",
        "update" => "can_update_users",
        "delete" => "can_delete_users",
    ];

    public function __construct(User $model, ManagementUserService $service)
    {
        $this->model = $model;
        $this->service = $service->initModel($model);
    }

    public function index()
    {
        (new Permission($this->permission ?? null))->can("view");
        $data = User::with(['unit', 'role'])->get();
        return view('management-user.index', [
            'data' => $data,
            'units' => Unit::all(),
            'roles' => Role::all()
        ]);
    }

    public function  create(Request $request){
        (new Permission($this->permission ?? null))->can("create");
        $field = collect([
            'name' => $request->name,
            'email' => $request->email,
            'unit_id' => $request->unit_id,
            'role_id' => $request->role_id,
            'password' => bcrypt(env("PASSWORD_DEFAULT", 123456))
        ]);

        User::create(
            $field->toArray()
        );

        return redirect()->route('management-user.index');
    }

    public function update(Request $request, $id) {

        (new Permission($this->permission ?? null))->can("update");
        $field = collect([
            'name' => $request->name,
            'email' => $request->email,
            'unit_id' => $request->unit_id,
            'role_id' => $request->role_id
        ]);
        // if ($id && $request->has('password')) {
        //     $field->put('password', $request->password);
        // }

        User::find($id)->update(
            $field->toArray()
        );

        return redirect()->route('management-user.index');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('management-user.index');
    }
}
