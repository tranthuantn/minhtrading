<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@minhtrading.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0901234567',
                'avatar' => 'avatars/admin.png',
                'bio' => 'Giảng viên trưởng & Nhà sáng lập hệ thống đào tạo Forex MinhTrading.',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}

