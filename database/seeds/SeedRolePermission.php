<?php

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SeedRolePermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = config("c_seed_permission")["permissions"];
        $seed_role_permission = config("c_seed_role_permission");

        foreach ($seed_role_permission as $role_name => $c_permission) {
            $role_data = Role::findByName($role_name);
            if (is_array($c_permission)) {
                foreach ($c_permission as $route => $perm_item) {
                    if (is_array($perm_item)) {
                        if (isset($perm_item["permissions"])) {
                            foreach (explode(",", $perm_item["permissions"]) as $item) {
                                $p_name = $permissions[$item]." ".$route;

                                $permission_data = Permission::where("name", "=", $p_name)->first();
                                if (!empty($permission_data)) {
                                    $role_data->givePermissionTo($permission_data);
                                }
                            }
                        }
                    }
                    // assign to "a,l,c,r,u,d"
                    elseif ($perm_item == "all") {
                        foreach ($permissions as $p_item) {
                            $permission_data = Permission::where("name", "=", $p_item." ".$route)->first();
                            if (!empty($permission_data)) {
                                $role_data->givePermissionTo($permission_data);
                            }
                        }
                    }
                }
            }
            // assign to all permission
            elseif ($c_permission == "all") {
                $role_data->syncPermissions(Permission::get());
            }
        }
    }
}
