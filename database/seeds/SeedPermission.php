<?php

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;

class SeedPermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $routes = config("c_seed_permission")["routes"];
        $permissions = config("c_seed_permission")["permissions"];

        foreach ($routes as $r_name => $route) {
            $r_permissions = $route["permissions"];
            foreach (explode(",", $r_permissions) as $item) {
                $p_name = $permissions[$item]." ".$r_name;
                $guard_name = "api";

                $p = Permission::firstOrNew([
                    "name" => $p_name,
                    "guard_name" => $guard_name,
                ]);
                $p->save();
            }
        }
    }
}
