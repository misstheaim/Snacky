<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Admin', 'email' => 'admin@admin.com', 'password' => Hash::make('admin'), 'role' => Role::where('role', config('app.admin_role'))->first()->id],
            ['name' => 'Manager', 'email' => 'manager@manager.com', 'password' => Hash::make('manager'), 'role' => Role::where('role', config('app.manager_role'))->first()->id],
            ['name' => 'Developer', 'email' => 'developer@developer.com', 'password' => Hash::make('developer'), 'role' => Role::where('role', config('app.dev_role'))->first()->id],
        ];

        User::upsert($data, ['email'], []);
    }
}
