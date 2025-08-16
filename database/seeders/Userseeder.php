<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // یک ادمین دستی بساز (phone: 09123456789 ، pass: admin123)
        User::factory()->admin()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'phone' => '09123456789',
            'password' => bcrypt('admin123'),
        ]);

        // چند یوزر نمونه (غیرفعال)
        User::factory(5)->create();
    }
}
