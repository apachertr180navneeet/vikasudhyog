<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name'              => 'Administrator',
                'username'          => 'admin',
                'email'             => 'admin@vikasudhyog.com',
                'password'          => Hash::make('admin123'),
                'role'              => 'Super Administrator',
                'phone'             => '+91 98765 43210',
                'status'            => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Rajesh Sharma',
                'username'          => 'rajesh.manager',
                'email'             => 'rajesh@vikasudhyog.com',
                'password'          => Hash::make('password123'),
                'role'              => 'Plant & Production Manager',
                'phone'             => '+91 94140 12345',
                'status'            => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Sunil Verma',
                'username'          => 'sunil.accounts',
                'email'             => 'accounts@vikasudhyog.com',
                'password'          => Hash::make('password123'),
                'role'              => 'Chief Accountant',
                'phone'             => '+91 94140 67890',
                'status'            => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Pooja Joshi',
                'username'          => 'pooja.sales',
                'email'             => 'sales@vikasudhyog.com',
                'password'          => Hash::make('password123'),
                'role'              => 'Sales & Dispatch Lead',
                'phone'             => '+91 94140 11223',
                'status'            => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Vikram Singh',
                'username'          => 'vikram.store',
                'email'             => 'store@vikasudhyog.com',
                'password'          => Hash::make('password123'),
                'role'              => 'Inventory & Store Incharge',
                'phone'             => '+91 94140 99887',
                'status'            => 'active',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Default ERP users and credentials successfully seeded!');
    }
}
