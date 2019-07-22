<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SeedRole::class);
        $this->call(SeedPermission::class);
        $this->call(SeedRolePermission::class);
    }
}
