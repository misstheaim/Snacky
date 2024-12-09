<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role' => config('app.admin_role')],
            ['role' => config('app.manager_role')],
            ['role' => config('app.dev_role')],
        ];

        Role::upsert($roles, ['role'], []);
    }
}
