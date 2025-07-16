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
        // Create some test users first
        User::factory(50)->create();

        // Create the default test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create admin user from environment variables
        User::factory()->create([
            'name' => env('ADMIN_NAME', 'Admin User'),
            'email' => env('ADMIN_EMAIL', 'admin@skvosh.com'),
            'password' => bcrypt(env('ADMIN_PASSWORD', 'password')),
        ]);

        // Run the club seeder (which will also create club-user relationships)
        $this->call([
            ClubSeeder::class,
            CourtSeeder::class,
        ]);
    }
}
