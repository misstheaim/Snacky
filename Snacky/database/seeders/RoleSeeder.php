<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = array(
            array('role' => config('app.admin_role')),
            array('role' => config('app.manager_role')),
            array('role' => config('app.dev_role')),
        );

        Role::upsert($roles, ['role'], []);
    }
}
