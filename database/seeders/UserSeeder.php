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
        // Remove non-admin users if any exist
        User::where('username', '!=', 'admin')
            ->where('email', '!=', 'admin@vikasudhyog.com')
            ->delete();

        // Seed exclusively the Administrator account
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'              => 'Administrator',
                'username'          => 'admin',
                'email'             => 'admin@vikasudhyog.com',
                'password'          => Hash::make('admin123'),
                'role'              => 'Super Administrator',
                'phone'             => '+91 98765 43210',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Administrator user seeded successfully (admin / admin123).');
    }
}
