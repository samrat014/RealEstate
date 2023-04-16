<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['guard_name'=>'admin-api', 'name' => 'create']);
        Permission::create(['guard_name'=>'admin-api', 'name' => 'read']);
        Permission::create(['guard_name'=>'admin-api', 'name' => 'update']);
        Permission::create(['guard_name'=>'admin-api', 'name' => 'delete']);

        // create roles and assign created permissions
        // or may be done by chaining

        $roleSuperAdmin = Role::create(['guard_name'=>'admin-api', 'name' => 'super-admin']);
        $roleSuperAdmin->givePermissionTo(Permission::all());

        $roleAdmin = Role::create(['guard_name'=>'admin-api', 'name' => 'admin'])
            ->givePermissionTo(['create', 'read']);

        $roleModerator = Role::create(['guard_name'=>'admin-api', 'name' => 'moderator'])
            ->givePermissionTo(['read']);

        /**
         * Give roles and permision to admin
         */
        $user1 = Admin::first();
        $user1->assignRole($roleSuperAdmin);

        $user2 = Admin::where('id', 2)->first();
        $user2->assignRole($roleAdmin);

        $user3 = Admin::where('id', 3)->first();
        $user3->assignRole($roleModerator);
    }
}
