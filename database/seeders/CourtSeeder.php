<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Court;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clubs = Club::all();

        if ($clubs->isEmpty()) {
            $this->command->info('No clubs found. Please run ClubSeeder first.');
            return;
        }

        foreach ($clubs as $club) {
            $this->createCourtsForClub($club);
        }
    }

    /**
     * Create courts for a specific club
     */
    private function createCourtsForClub(Club $club): void
    {
        $clubSports = $club->sports ?? ['squash'];
        $courtCount = rand(2, 8); // Each club gets 2-8 courts

        // Determine how many courts of each type based on club's sports
        $courtDistribution = $this->distributeCourtsByType($clubSports, $courtCount);

        $courtNumber = 1;

        foreach ($courtDistribution as $sport => $count) {
            for ($i = 0; $i < $count; $i++) {
                $this->createCourt($club, $sport, $courtNumber);
                $courtNumber++;
            }
        }
    }

    /**
     * Distribute courts by type based on club's supported sports
     */
    private function distributeCourtsByType(array $sports, int $totalCourts): array
    {
        $distribution = [];

        // Always prioritize squash as the primary sport
        if (in_array('squash', $sports)) {
            $distribution['squash'] = max(1, intval($totalCourts * 0.6)); // At least 60% squash
        }

        $remaining = $totalCourts - ($distribution['squash'] ?? 0);

        // Distribute remaining courts among other sports
        $otherSports = array_diff($sports, ['squash']);

        if (!empty($otherSports) && $remaining > 0) {
            $sportsToUse = array_slice($otherSports, 0, min(count($otherSports), $remaining));

            foreach ($sportsToUse as $sport) {
                if ($remaining > 0) {
                    $distribution[$sport] = max(1, intval($remaining / count($sportsToUse)));
                    $remaining -= $distribution[$sport];
                }
            }
        }

        // If we still have remaining courts, add them to squash
        if ($remaining > 0) {
            $distribution['squash'] = ($distribution['squash'] ?? 0) + $remaining;
        }

        return array_filter($distribution);
    }

    /**
     * Create a single court
     */
    private function createCourt(Club $club, string $type, int $number): void
    {
        $courtData = [
            'club_id' => $club->id,
            'name' => 'Court ' . $number,
            'type' => $type,
            'sort_order' => $number,
            'is_active' => true,
        ];

        // Add type-specific data
        $courtData = array_merge($courtData, $this->getTypeSpecificData($type));

        // Create premium courts occasionally
        if (rand(1, 100) <= 20) { // 20% chance of premium court
            $courtData = array_merge($courtData, $this->getPremiumCourtData());
        }

        Court::create($courtData);
    }

    /**
     * Get type-specific court data
     */
    private function getTypeSpecificData(string $type): array
    {
        $typeData = [
            'squash' => [
                'max_players' => 2,
                'surface_type' => fake()->randomElement(['hardwood', 'synthetic', 'glass back wall']),
                'hourly_rate' => fake()->randomFloat(2, 25, 45),
                'description' => 'Professional squash court with regulation dimensions',
            ],
            'tennis' => [
                'max_players' => 4,
                'surface_type' => fake()->randomElement(['hard court', 'clay', 'synthetic']),
                'hourly_rate' => fake()->randomFloat(2, 30, 60),
                'description' => 'Full-size tennis court suitable for singles and doubles',
            ],
            'badminton' => [
                'max_players' => 4,
                'surface_type' => fake()->randomElement(['wooden', 'synthetic', 'pvc']),
                'hourly_rate' => fake()->randomFloat(2, 20, 35),
                'description' => 'Regulation badminton court with proper height clearance',
            ],
            'racquetball' => [
                'max_players' => 2,
                'surface_type' => fake()->randomElement(['hardwood', 'synthetic']),
                'hourly_rate' => fake()->randomFloat(2, 25, 40),
                'description' => 'Enclosed racquetball court with four walls',
            ],
            'table_tennis' => [
                'max_players' => 2,
                'surface_type' => fake()->randomElement(['indoor wooden floor', 'synthetic']),
                'hourly_rate' => fake()->randomFloat(2, 15, 25),
                'description' => 'Table tennis setup with tournament-grade table',
            ],
        ];

        $data = $typeData[$type] ?? $typeData['squash'];

        // Add basic amenities
        $data['amenities'] = fake()->randomElements([
            'air_conditioning',
            'heating',
            'water_fountain',
            'equipment_storage'
        ], fake()->numberBetween(1, 3));

        return $data;
    }

    /**
     * Get premium court upgrades
     */
    private function getPremiumCourtData(): array
    {
        return [
            'amenities' => [
                'air_conditioning',
                'heating',
                'viewing_gallery',
                'sound_system',
                'video_recording',
                'towel_service',
                'coaching_available'
            ],
            'equipment_included' => 'Premium equipment and towels included',
            'notes' => 'Premium court with enhanced amenities and professional features',
            'description' => 'Premium court featuring state-of-the-art facilities and professional-grade equipment',
        ];
    }
}
