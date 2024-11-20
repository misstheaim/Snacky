<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerifySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = array();
        $verified_users = DB::table('filament_email_2fa_verify')->get();

        foreach (User::all() as $user) {
            if ($verified_users->where('user_id', $user->id)->count() === 0) {
                $data[] = [
                    'updated_at' => now(),
                    'created_at' => now(),
                    'user_type' => User::class,
                    'user_id' => $user->id,
                    'session_id' => 1,
                ];
            } 
        }

        DB::table('filament_email_2fa_verify')->upsert($data, [], []);
    }
}
