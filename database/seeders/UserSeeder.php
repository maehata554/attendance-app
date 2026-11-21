<?php

namespace Database\Seeders;
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
        //ユーザー１（一般ユーザー）
        User::factory()->create([
            'name' => 'ユーザー１',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 0,
        ]);
        //ユーザー２（一般ユーザー）
        User::factory()->create([
            'name' => 'ユーザー２',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 0,
        ]);
        //ユーザー３（管理者ユーザー）
        User::factory()->create([
            'name' => '管理者',
            'email' => 'user3@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 1,
        ]);
    }
}
