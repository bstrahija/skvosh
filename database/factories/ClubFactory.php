<?php

namespace Database\Factories;

use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Club>
 */
class ClubFactory extends Factory
{
    protected $model = Club::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company . ' ' . $this->faker->randomElement(['Sports Club', 'Racquet Club', 'Athletic Club', 'Squash Club', 'Tennis Club']);

        // Generate realistic coordinates for major cities
        $coordinates = $this->getRandomCityCoordinates();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraphs(3, true),
            'email' => $this->faker->companyEmail,
            'phone' => $this->faker->phoneNumber,
            'website' => $this->faker->optional(0.7)->url,
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->optional(0.3)->secondaryAddress,
            'city' => $coordinates['city'],
            'state_province' => $coordinates['state'],
            'postal_code' => $this->faker->postcode,
            'country' => $coordinates['country'],
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lng'],
            'is_active' => $this->faker->boolean(85), // 85% chance of being active
            'operating_hours' => $this->generateOperatingHours(),
            'amenities' => $this->generateAmenities(),
            'sports' => $this->generateSports(),
            'logo_path' => null, // Will be filled by seeder if needed
            'gallery_images' => [],
        ];
    }

    /**
     * Generate realistic operating hours
     */
    private function generateOperatingHours(): array
    {
        $hours = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            if ($this->faker->boolean(90)) { // 90% chance of being open
                $openTime = $this->faker->randomElement(['06:00', '07:00', '08:00']);
                $closeTime = $this->faker->randomElement(['21:00', '22:00', '23:00']);

                $hours[$day] = [
                    'open' => $openTime,
                    'close' => $closeTime,
                    'is_open' => true,
                ];
            } else {
                $hours[$day] = [
                    'is_open' => false,
                ];
            }
        }

        return $hours;
    }

    /**
     * Generate realistic amenities
     */
    private function generateAmenities(): array
    {
        $allAmenities = [
            'parking',
            'locker_rooms',
            'showers',
            'pro_shop',
            'restaurant',
            'bar',
            'fitness_center',
            'pool',
            'sauna',
            'steam_room',
            'massage',
            'child_care',
            'wifi',
            'equipment_rental',
            'coaching',
            'lessons',
        ];

        return $this->faker->randomElements($allAmenities, $this->faker->numberBetween(3, 10));
    }

    /**
     * Generate sports offerings
     */
    private function generateSports(): array
    {
        $allSports = ['squash', 'tennis', 'badminton', 'racquetball', 'table_tennis'];

        // Always include at least squash
        $sports = ['squash'];

        // Add other sports randomly
        $additionalSports = $this->faker->randomElements(
            array_diff($allSports, ['squash']),
            $this->faker->numberBetween(0, 3)
        );

        return array_merge($sports, $additionalSports);
    }

    /**
     * Get random city coordinates
     */
    private function getRandomCityCoordinates(): array
    {
        $cities = [
            // Major US Cities
            ['city' => 'New York', 'state' => 'New York', 'country' => 'United States', 'lat' => 40.7128, 'lng' => -74.0060],
            ['city' => 'Los Angeles', 'state' => 'California', 'country' => 'United States', 'lat' => 34.0522, 'lng' => -118.2437],
            ['city' => 'Chicago', 'state' => 'Illinois', 'country' => 'United States', 'lat' => 41.8781, 'lng' => -87.6298],
            ['city' => 'Houston', 'state' => 'Texas', 'country' => 'United States', 'lat' => 29.7604, 'lng' => -95.3698],
            ['city' => 'Boston', 'state' => 'Massachusetts', 'country' => 'United States', 'lat' => 42.3601, 'lng' => -71.0589],
            ['city' => 'San Francisco', 'state' => 'California', 'country' => 'United States', 'lat' => 37.7749, 'lng' => -122.4194],

            // Major Canadian Cities
            ['city' => 'Toronto', 'state' => 'Ontario', 'country' => 'Canada', 'lat' => 43.6532, 'lng' => -79.3832],
            ['city' => 'Vancouver', 'state' => 'British Columbia', 'country' => 'Canada', 'lat' => 49.2827, 'lng' => -123.1207],
            ['city' => 'Montreal', 'state' => 'Quebec', 'country' => 'Canada', 'lat' => 45.5017, 'lng' => -73.5673],

            // Major UK Cities
            ['city' => 'London', 'state' => 'England', 'country' => 'United Kingdom', 'lat' => 51.5074, 'lng' => -0.1278],
            ['city' => 'Manchester', 'state' => 'England', 'country' => 'United Kingdom', 'lat' => 53.4808, 'lng' => -2.2426],
            ['city' => 'Birmingham', 'state' => 'England', 'country' => 'United Kingdom', 'lat' => 52.4862, 'lng' => -1.8904],

            // Major Australian Cities
            ['city' => 'Sydney', 'state' => 'New South Wales', 'country' => 'Australia', 'lat' => -33.8688, 'lng' => 151.2093],
            ['city' => 'Melbourne', 'state' => 'Victoria', 'country' => 'Australia', 'lat' => -37.8136, 'lng' => 144.9631],
        ];

        $baseLocation = $this->faker->randomElement($cities);

        // Add some variation to coordinates (within ~10km radius)
        $baseLocation['lat'] += $this->faker->randomFloat(4, -0.1, 0.1);
        $baseLocation['lng'] += $this->faker->randomFloat(4, -0.1, 0.1);

        return $baseLocation;
    }

    /**
     * State to create premium clubs
     */
    public function premium(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'amenities' => [
                    'parking',
                    'locker_rooms',
                    'showers',
                    'pro_shop',
                    'restaurant',
                    'bar',
                    'fitness_center',
                    'pool',
                    'sauna',
                    'steam_room',
                    'massage',
                    'wifi',
                    'equipment_rental',
                    'coaching',
                    'lessons'
                ],
                'sports' => ['squash', 'tennis', 'badminton', 'racquetball', 'table_tennis'],
                'is_active' => true,
            ];
        });
    }

    /**
     * State to create squash-only clubs
     */
    public function squashOnly(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => $this->faker->company . ' Squash Club',
                'sports' => ['squash'],
                'amenities' => ['parking', 'locker_rooms', 'showers', 'equipment_rental', 'coaching'],
            ];
        });
    }
}
