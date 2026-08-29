<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'chhabrasports',
                'phone' => '1234567890',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'Admin',
            ]
        );

        $this->call([
            CategorySeeder::class,
            BrandSeeder::class,
            TagSeeder::class,
            ProductSeeder::class
        ]);
    }
}
