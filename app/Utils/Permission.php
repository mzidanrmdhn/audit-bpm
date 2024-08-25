<?php
namespace App\Utils;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Diatria\LaravelInstant\Utils\Helper;
use Diatria\LaravelInstant\Utils\ErrorException;

class Permission
{
    protected $permission, $action;
    public function __construct(array $permission)
    {
        $this->permission = $permission;
    }

    public function can($action)
    {
        $this->action = $action;

        $user = User::where("id", Auth::id())->first();

        if (!$user) {
            // return view('auth.login');
            throw new Exception("Unauthorized", 401);
        }

        $haveAccess = in_array(
            $this->getAction(),
            $user->permissions->toArray()
        );

        if (!$haveAccess) {
            throw new ErrorException("Permission denied", 403);
        }

        return $haveAccess;
    }

    public function getAction()
    {
        return Helper::get($this->permission, $this->action);
    }
}
