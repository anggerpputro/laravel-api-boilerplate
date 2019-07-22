<?php

use Illuminate\Database\Seeder;

use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Models\Role;

class SeedRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (config('c_roles') as $key => $value) {
            try {
                $role = Role::findByName($value, "api");
            } catch (RoleDoesNotExist $ex) {
                $role = Role::create(["guard_name" => "api", "name" => $value]);
            }
        }
    }
}
