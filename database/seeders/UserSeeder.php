<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // ✅ Ensure there is always ONE Admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'], 
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'isAdmin' => 0, // Admin role
            ],
        );

        // ✅ Create 19 Regular Users
        for ($i = 1; $i <= 19; $i++) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make($faker->internet->password()),
                'isAdmin' => 1, // Regular user
            ]);
        }
    }
}
