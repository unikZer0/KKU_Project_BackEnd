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
        // seed default roles/users
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        User::factory()->create([
            'name' => 'Borrower User',
            'email' => 'borrower@example.com',
            'password' => bcrypt('password'),
            'role' => 'borrower',
        ]);

        // Call the LogSeeder to create sample log data
        $this->call(LogSeeder::class);
    }
}
