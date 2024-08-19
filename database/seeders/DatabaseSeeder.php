<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use Diatria\LaravelInstant\Models\Permission;
use Diatria\LaravelInstant\Models\Role;
use Diatria\LaravelInstant\Models\RolePermission;
use Diatria\LaravelInstant\Utils\Permission as UtilsPermission;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('role_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('users')->truncate();
        DB::table('units')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $permissions = [
            'can_create_management_units',
            'can_view_management_units',
            'can_update_management_units',
            'can_delete_management_units',

            'can_create_users',
            'can_view_users',
            'can_update_users',
            'can_delete_users',

            'can_create_units',
            'can_view_units',
            'can_update_units',
            'can_delete_units',

            'can_create_unit_details',
            'can_view_unit_details',
            'can_update_unit_details',
            'can_delete_unit_details',

            'can_view_management_unit',
            'can_store_management_unit',
            'can_delete_management_unit',

            'can_view_criteria',
            'can_store_criteria',
            'can_delete_criteria',

            'can_view_sub_criteria',
            'can_store_sub_criteria',
            'can_delete_sub_criteria',

            'can_view_target',
            'can_save_target',

            'can_view_achievement',
            'can_save_achievement',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission
            ]);
        }

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt(123456),
            'role_id' => Role::create(['name' => 'Super Admin'])->id,
            'unit_id' => Unit::create(['name' => 'admin'])->id
        ]);

        User::create([
            'name' => 'Auditor',
            'email' => 'auditor@gmail.com',
            'password' => bcrypt(123456),
            'role_id' => Role::create(['name' => 'Auditor'])->id,
            'unit_id' => Unit::create(['name' => 'BPM'])->id
        ]);

        User::create([
            'name' => 'Unit',
            'email' => 'unit@gmail.com',
            'password' => bcrypt(123456),
            'role_id' => Role::create(['name' => 'Unit'])->id,
            'unit_id' => Unit::create(['name' => 'Prodi S1 Teknologi Informasi'])->id
        ]);

        foreach (DB::table('permissions')->pluck('id') as $permissionID) {
            RolePermission::create([
                'role_id' => 1,
                'permission_id' => $permissionID
            ]);
        }

        foreach (DB::table('permissions')->pluck('id') as $permissionID) {
            RolePermission::create([
                'role_id' => 2,
                'permission_id' => $permissionID
            ]);
        }
    }
}
