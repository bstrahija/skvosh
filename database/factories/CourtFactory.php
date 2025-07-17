<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Court;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Court>
 */
class CourtFactory extends Factory
{
    protected $model = Court::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['squash', 'tennis', 'badminton', 'racquetball', 'table_tennis']);

        return [
            'club_id' => Club::factory(),
            'name' => 'Court ' . $this->faker->numberBetween(1, 10),
            'type' => $type,
            'description' => $this->faker->optional(0.6)->sentence(),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'hourly_rate' => $this->getHourlyRateForType($type),
            'amenities' => $this->generateCourtAmenities(),
            'available_hours' => null, // Will use club hours by default
            'max_players' => $this->getMaxPlayersForType($type),
            'surface_type' => $this->getSurfaceTypeForType($type),
            'equipment_included' => $this->faker->optional(0.4)->randomElement([
                'Racquets available for rent',
                'Balls provided',
                'Racquets and balls included',
                'Equipment rental available',
            ]),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'sort_order' => $this->faker->numberBetween(1, 20),
        ];
    }

    /**
     * Get hourly rate based on court type
     */
    private function getHourlyRateForType(string $type): float
    {
        $rates = [
            'squash' => [25, 45],
            'tennis' => [30, 60],
            'badminton' => [20, 35],
            'racquetball' => [25, 40],
            'table_tennis' => [15, 25],
        ];

        $range = $rates[$type] ?? [20, 40];
        return $this->faker->randomFloat(2, $range[0], $range[1]);
    }

    /**
     * Get max players based on court type
     */
    private function getMaxPlayersForType(string $type): int
    {
        $maxPlayers = [
            'squash' => 2,
            'tennis' => 4,
            'badminton' => 4,
            'racquetball' => 2,
            'table_tennis' => 2,
        ];

        return $maxPlayers[$type] ?? 2;
    }

    /**
     * Get surface type based on court type
     */
    private function getSurfaceTypeForType(string $type): string
    {
        $surfaces = [
            'squash' => ['hardwood', 'synthetic', 'glass back wall'],
            'tennis' => ['hard court', 'clay', 'grass', 'synthetic'],
            'badminton' => ['wooden', 'synthetic', 'pvc'],
            'racquetball' => ['hardwood', 'synthetic'],
            'table_tennis' => ['indoor wooden floor', 'synthetic'],
        ];

        $options = $surfaces[$type] ?? ['synthetic'];
        return $this->faker->randomElement($options);
    }

    /**
     * Generate court-specific amenities
     */
    private function generateCourtAmenities(): array
    {
        $allAmenities = [
            'air_conditioning',
            'heating',
            'viewing_gallery',
            'sound_system',
            'video_recording',
            'ball_machine',
            'towel_service',
            'water_fountain',
            'equipment_storage',
            'coaching_available',
        ];

        return $this->faker->randomElements($allAmenities, $this->faker->numberBetween(1, 5));
    }

    /**
     * State for squash courts
     */
    public function squash(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'squash',
                'max_players' => 2,
                'surface_type' => $this->faker->randomElement(['hardwood', 'synthetic', 'glass back wall']),
                'hourly_rate' => $this->faker->randomFloat(2, 25, 45),
            ];
        });
    }

    /**
     * State for tennis courts
     */
    public function tennis(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'tennis',
                'max_players' => 4,
                'surface_type' => $this->faker->randomElement(['hard court', 'clay', 'synthetic']),
                'hourly_rate' => $this->faker->randomFloat(2, 30, 60),
            ];
        });
    }

    /**
     * State for premium courts with all amenities
     */
    public function premium(): static
    {
        return $this->state(function (array $attributes) {
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
                'hourly_rate' => $this->faker->randomFloat(2, 50, 80),
                'equipment_included' => 'Premium equipment and towels included',
                'surface_type' => 'Premium ' . ($attributes['surface_type'] ?? 'hardwood'),
            ];
        });
    }

    /**
     * State for creating courts with sequential naming
     */
    public function sequential(int $number): static
    {
        return $this->state(function (array $attributes) use ($number) {
            return [
                'name' => 'Court ' . $number,
                'sort_order' => $number,
            ];
        });
    }
}
