<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some featured/example clubs with specific data
        $this->createFeaturedClubs();

        // Create random clubs using factory
        Club::factory(25)->create();

        // Create some premium clubs
        Club::factory(5)->premium()->create();

        // Create some squash-only clubs
        Club::factory(8)->squashOnly()->create();

        // Attach users to clubs with various roles
        $this->attachUsersToClubs();
    }

    /**
     * Create featured clubs with specific, realistic data
     */
    private function createFeaturedClubs(): void
    {
        $featuredClubs = [
            [
                'name' => 'Metropolitan Squash Club',
                'description' => 'Premier squash facility in the heart of downtown. Founded in 1985, we offer world-class courts and professional coaching for all skill levels. Our club has hosted numerous tournaments and is home to several national champions.',
                'email' => 'info@metrosquash.com',
                'phone' => '+1 (555) 123-4567',
                'website' => 'https://metrosquash.com',
                'address_line_1' => '123 Championship Drive',
                'city' => 'New York',
                'state_province' => 'New York',
                'postal_code' => '10001',
                'country' => 'United States',
                'latitude' => 40.7505,
                'longitude' => -73.9934,
                'sports' => ['squash', 'tennis'],
                'amenities' => ['parking', 'locker_rooms', 'showers', 'pro_shop', 'restaurant', 'fitness_center', 'coaching', 'lessons'],
            ],
            [
                'name' => 'Riverside Racquet & Fitness',
                'description' => 'Multi-sport facility offering squash, tennis, and fitness services. Beautiful riverside location with panoramic views. Family-friendly environment with programs for all ages.',
                'email' => 'welcome@riversideracquet.com',
                'phone' => '+1 (555) 987-6543',
                'website' => 'https://riversideracquet.com',
                'address_line_1' => '456 Riverside Boulevard',
                'city' => 'Chicago',
                'state_province' => 'Illinois',
                'postal_code' => '60601',
                'country' => 'United States',
                'latitude' => 41.8786,
                'longitude' => -87.6251,
                'sports' => ['squash', 'tennis', 'badminton', 'table_tennis'],
                'amenities' => ['parking', 'locker_rooms', 'showers', 'pro_shop', 'bar', 'fitness_center', 'pool', 'child_care', 'coaching'],
            ],
            [
                'name' => 'Elite Sports Academy',
                'description' => 'High-performance training facility specializing in racquet sports. State-of-the-art courts with advanced booking systems and performance analytics. Professional coaching staff with international experience.',
                'email' => 'academy@elitesports.ca',
                'phone' => '+1 (416) 555-0123',
                'website' => 'https://elitesportsacademy.ca',
                'address_line_1' => '789 Sports Complex Way',
                'city' => 'Toronto',
                'state_province' => 'Ontario',
                'postal_code' => 'M5V 3A8',
                'country' => 'Canada',
                'latitude' => 43.6426,
                'longitude' => -79.3871,
                'sports' => ['squash', 'tennis', 'badminton', 'racquetball'],
                'amenities' => ['parking', 'locker_rooms', 'showers', 'pro_shop', 'restaurant', 'fitness_center', 'sauna', 'massage', 'coaching', 'lessons'],
            ],
        ];

        foreach ($featuredClubs as $clubData) {
            $clubData['slug'] = Str::slug($clubData['name']);
            $clubData['is_active'] = true;
            $clubData['operating_hours'] = $this->getStandardOperatingHours();
            $clubData['gallery_images'] = [];

            Club::create($clubData);
        }
    }

    /**
     * Get standard operating hours
     */
    private function getStandardOperatingHours(): array
    {
        return [
            'monday' => ['open' => '06:00', 'close' => '22:00', 'is_open' => true],
            'tuesday' => ['open' => '06:00', 'close' => '22:00', 'is_open' => true],
            'wednesday' => ['open' => '06:00', 'close' => '22:00', 'is_open' => true],
            'thursday' => ['open' => '06:00', 'close' => '22:00', 'is_open' => true],
            'friday' => ['open' => '06:00', 'close' => '22:00', 'is_open' => true],
            'saturday' => ['open' => '07:00', 'close' => '21:00', 'is_open' => true],
            'sunday' => ['open' => '08:00', 'close' => '20:00', 'is_open' => true],
        ];
    }

    /**
     * Attach users to clubs with various roles
     */
    private function attachUsersToClubs(): void
    {
        $users = User::all();
        $clubs = Club::all();

        if ($users->isEmpty() || $clubs->isEmpty()) {
            return;
        }

        foreach ($clubs as $club) {
            // Ensure each club has at least one owner
            $owner = $users->random();
            $club->users()->attach($owner->id, [
                'role' => 'owner',
                'joined_at' => now()->subMonths(rand(1, 24)),
                'is_active' => true,
                'permissions' => json_encode(['manage_club', 'manage_users', 'manage_bookings', 'view_reports']),
            ]);

            // Add 1-2 admins per club
            $adminCount = rand(1, 2);
            $potentialAdmins = $users->except($owner->id)->random(min($adminCount, $users->count() - 1));

            foreach ($potentialAdmins as $admin) {
                $club->users()->attach($admin->id, [
                    'role' => 'admin',
                    'joined_at' => now()->subMonths(rand(1, 18)),
                    'is_active' => true,
                    'permissions' => json_encode(['manage_bookings', 'view_reports']),
                ]);
            }

            // Add 5-15 members per club
            $memberCount = rand(5, 15);
            $potentialMembers = $users->except([$owner->id, ...$potentialAdmins->pluck('id')->toArray()])
                ->random(min($memberCount, max(0, $users->count() - 1 - $adminCount)));

            foreach ($potentialMembers as $member) {
                $joinedAt = now()->subMonths(rand(1, 36));
                $isActive = rand(1, 100) <= 90; // 90% active memberships

                $club->users()->attach($member->id, [
                    'role' => 'member',
                    'joined_at' => $joinedAt,
                    'expires_at' => $isActive ? $joinedAt->copy()->addYear() : null,
                    'is_active' => $isActive,
                    'permissions' => json_encode(['book_courts']),
                ]);
            }
        }
    }
}
