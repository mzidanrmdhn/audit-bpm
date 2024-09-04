<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\ActivityLog;
use Diatria\LaravelInstant\Models\Role;
use Illuminate\Notifications\Notifiable;
use Diatria\LaravelInstant\Models\RolePermission;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'unit_id',
        'role_id'
    ];

    public function units()
    {
        return $this->belongsToMany(Unit::class);
    }

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function permissions(): Attribute
    {
        return new Attribute(
            get: function () {
                return RolePermission::where("role_id", $this->role_id)
                    ->join(
                        "permissions",
                        "role_permissions.permission_id",
                        "permissions.id"
                    )
                    ->pluck("permissions.name");
            }
        );
    }
}
