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
        // ✅ Ensure there is always ONE Admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'], 
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'isAdmin' => 0, // Admin role
            ],
        );

        // ✅ Create fixed users without Faker
        $users = [
            ['name' => 'User 1', 'email' => 'user1@example.com', 'password' => 'userpassword1'],
            ['name' => 'User 2', 'email' => 'user2@example.com', 'password' => 'userpassword2'],
            ['name' => 'User 3', 'email' => 'user3@example.com', 'password' => 'userpassword3'],
            ['name' => 'User 4', 'email' => 'user4@example.com', 'password' => 'userpassword4'],
            ['name' => 'User 5', 'email' => 'user5@example.com', 'password' => 'userpassword5'],
            ['name' => 'User 6', 'email' => 'user6@example.com', 'password' => 'userpassword6'],
            ['name' => 'User 7', 'email' => 'user7@example.com', 'password' => 'userpassword7'],
            ['name' => 'User 8', 'email' => 'user8@example.com', 'password' => 'userpassword8'],
            ['name' => 'User 9', 'email' => 'user9@example.com', 'password' => 'userpassword9'],
            ['name' => 'User 10', 'email' => 'user10@example.com', 'password' => 'userpassword10'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], 
                [
                    'name' => $user['name'],
                    'password' => Hash::make($user['password']),
                    'isAdmin' => 1, // Regular user
                ]
            );
        }
    }
}
