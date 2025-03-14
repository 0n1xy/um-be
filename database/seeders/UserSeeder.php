<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ✅ Ensure an admin user exists
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'isAdmin' => 0, // Admin role
            ]
        );

        // ✅ Generate 30 users with unique email and names
        $users = [];

        for ($i = 1; $i <= 20; $i++) {
            $users[] = [
                'name' => "User $i " . ["Smith", "Nguyen", "Tran", "Johnson", "Brown", "Lee", "Garcia", "Martinez", "Davis", "Hernandez"][array_rand(["Smith", "Nguyen", "Tran", "Johnson", "Brown", "Lee", "Garcia", "Martinez", "Davis", "Hernandez"])],
                'email' => "user$i" . ["@mail.com", "@test.com", "@example.com", "@domain.com"][array_rand(["@mail.com", "@test.com", "@example.com", "@domain.com"])],
                'password' => Hash::make("userpassword$i"),
                'isAdmin' => rand(0, 1), // Randomly assign role
            ];
        }

        User::insert($users);
    }
}
