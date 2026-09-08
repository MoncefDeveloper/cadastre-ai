<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // 1. Root Master Admin (From .env)
            [
                'id' => 1,
                'name' => (string) env('SUPER_ADMIN_NAME', 'Moncef Dev'),
                'email' => (string) env('SUPER_ADMIN_EMAIL', 'matchmaker@moncefdev.me'),
                'password' => Hash::make((string) env('SUPER_ADMIN_PASSWORD', 'moncefdev')),
                'is_active' => true,
                'role' => 'super_admin',
            ],
            // 2. Demo Admin
            [
                'id' => 2,
                'name' => 'Admin Demo',
                'email' => 'admin@matchmaker.test',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Admin', // Exact match with ShieldSeeder
            ],
            // 3. Demo Manager
            [
                'id' => 3,
                'name' => 'Manager Demo',
                'email' => 'manager@matchmaker.test',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Broker Manager', // Exact match with ShieldSeeder
            ],
            // 4. Demo Agent
            [
                'id' => 4,
                'name' => 'Agent Demo',
                'email' => 'agent@matchmaker.test',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Senior Agent', // Exact match with ShieldSeeder
            ],
            // 5. Demo Guest
            [
                'id' => 5,
                'name' => 'Guest Demo',
                'email' => 'guest@matchmaker.test',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Guest Viewer', // Exact match with ShieldSeeder
            ],
            // 6. Moncef Ross (Listing Lead)
            [
                'id' => 6,
                'name' => 'Moncef Ross',
                'email' => 'listings@matchmaker.test',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Listing Specialist', // Exact match with ShieldSeeder
            ],
            // 7. Sarah Rostova (Auditor)
            [
                'id' => 7,
                'name' => 'Sarah Rostova',
                'email' => 'auditor@matchmaker.test',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Compliance Auditor', // Exact match with ShieldSeeder
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['id' => $userData['id']],
                $userData
            );

            $user->syncRoles([$role]);
        }
    }
}
