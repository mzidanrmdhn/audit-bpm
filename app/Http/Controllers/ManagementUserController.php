<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use App\Utils\Permission;
use Illuminate\Http\Request;
use App\Services\ManagementUserService;
use Diatria\LaravelInstant\Models\Role;
use Diatria\LaravelInstant\Traits\InstantControllerTrait;
use Illuminate\Support\Facades\Hash;

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

    public function store(Request $request, $id = null)
    {
        (new Permission($this->permission ?? null))->can("create");
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'unit_id' => 'required|exists:units,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'unit_id' => $validatedData['unit_id'],
            'role_id' => $validatedData['role_id']
        ];
        if ($request->has('password')) {
            $updateData['password'] = bcrypt(env("PASSWORD_DEFAULT", 123456));
        }

        User::updateOrCreate(
            ['id' => $id],
            $updateData
        );

        return redirect()->route('management-user.index')->with('success', 'User saved successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('management-user.index');
    }


    public function updatePasswordForm($id)
    {
        $user = User::findOrFail($id);
        return view('management-user.change-password', compact('user'));
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'old_password.required' => 'Password lama perlu diisi',
            'new_password.required' => 'Password baru perlu diisi',
            'new_password.min' => 'Password baru harus terdiri dari minimal 6 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        $user = User::findOrFail($id);

        if (!Hash::check($request->old_password, $user->password)) {
            return redirect()->back()->withErrors(['old_password' => 'Password lama tidak cocok.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('user.changePasswordForm', ['id' => $id])->with('success', 'Password berhasil diganti');
    }
}
