<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Log;
use App\Models\User;

class LogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run user seeder first.');
            return;
        }

        $actions = ['create', 'update', 'delete', 'login', 'logout'];
        
        // Create 50 sample log entries
        for ($i = 0; $i < 50; $i++) {
            Log::create([
                'users_id' => $users->random()->id,
                'action' => $actions[array_rand($actions)],
                'ip_address' => $this->generateRandomIP(),
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
                'updated_at' => now(),
            ]);
        }
    }

    private function generateRandomIP()
    {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254);
    }
}
