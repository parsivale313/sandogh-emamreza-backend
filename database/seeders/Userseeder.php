<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // یک ادمین ایجاد کن
        User::factory()->admin()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '09123456789',
            'email' => 'admin@example.com',
        ]);

        // چند کاربر معمولی
        User::factory(10)->create();
    }
}
