<?php

namespace Database\Seeders;

use App\Domain\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@b2bmarketplace.com',
            'password' => Hash::make('mariposogigante'), // Change this in production
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);
    }
}
