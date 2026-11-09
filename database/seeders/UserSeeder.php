<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dedicated Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
        [
            'name' => 'System Administrator',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        // Dedicated User
        User::firstOrCreate(
            ['email' => 'user@example.com'],
        [
            'name' => 'Regular User',
            'password' => Hash::make('password123'),
            'role' => 'user'
        ]);

        // create random users
        User::factory(5)->create();

        // DB::table('users')->insert([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        //     'password' => Hash::make('password123'),
        //     'role' => 'user'
        // ]);

        // User::factory()->count(10)->create();

        // User::factory()->count(3)->suspended()->create();

        // User::factory()
        //     ->hasPosts(3)
        //     ->create();
    }
}
