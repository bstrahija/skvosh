<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Competition;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competition>
 */
class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->generateCompetitionName();
        $type = fake()->randomElement(Competition::TYPES);
        $format = $this->getFormatForType($type);
        $sport = fake()->randomElement(['squash', 'tennis', 'badminton', 'racquetball', 'table_tennis']);

        // Generate realistic dates
        $registrationStart = fake()->dateTimeBetween('-30 days', '+60 days');
        $registrationEnd = Carbon::instance($registrationStart)->addDays(rand(7, 30));
        $startDate = Carbon::instance($registrationEnd)->addDays(rand(1, 14));
        $endDate = Carbon::instance($startDate)->addDays($this->getDurationForType($type));

        return [
            'club_id' => Club::factory(),
            'created_by' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1000, 9999),
            'description' => $this->generateDescription($type, $sport),
            'type' => $type,
            'format' => $format,
            'sport' => $sport,
            'status' => fake()->randomElement(['draft', 'open', 'in_progress', 'completed']),
            'is_ranked' => fake()->boolean(70), // 70% chance of being ranked
            'is_public' => fake()->boolean(85), // 85% chance of being public
            'max_participants' => $this->getMaxParticipantsForType($type),
            'min_participants' => $this->getMinParticipantsForType($type),
            'entry_fee' => fake()->optional(0.6)->randomFloat(2, 10, 100), // 60% chance of entry fee
            'prize_structure' => $this->generatePrizeStructure(),
            'registration_start' => $registrationStart,
            'registration_end' => $registrationEnd,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'rules' => $this->generateRules($type, $sport),
            'settings' => $this->generateSettings($type),
            'requirements' => fake()->optional(0.4)->sentence(),
            'image_path' => null,
            'auto_schedule' => fake()->boolean(30), // 30% chance of auto-scheduling
            'rounds_completed' => 0,
            'total_rounds' => $this->getTotalRoundsForType($type),
        ];
    }

    /**
     * Generate realistic competition names
     */
    private function generateCompetitionName(): string
    {
        $prefixes = [
            'Annual',
            'Spring',
            'Summer',
            'Fall',
            'Winter',
            'Monthly',
            'Weekly',
            'Championship',
            'Open',
            'Masters',
            'Amateur',
            'Professional',
            'Elite',
            'Junior',
            'Senior',
            'Novice',
            'Intermediate',
            'Advanced'
        ];

        $types = [
            'Tournament',
            'Championship',
            'League',
            'Cup',
            'Classic',
            'Challenge',
            'Series',
            'Masters',
            'Open',
            'Invitational',
            'Festival'
        ];

        $prefix = fake()->randomElement($prefixes);
        $type = fake()->randomElement($types);
        $year = fake()->year();

        return $prefix . ' ' . $type . ' ' . $year;
    }

    /**
     * Generate competition description
     */
    private function generateDescription(string $type, string $sport): string
    {
        $templates = [
            'tournament' => "Join our exciting {sport} tournament featuring competitive matches and prizes for top performers.",
            'league' => "Regular {sport} league play with weekly matches and season-long standings.",
            'single_match' => "Individual {sport} match between skilled players.",
            'ladder' => "Climb the {sport} ladder by challenging and defeating players above you.",
            'round_robin' => "Round-robin {sport} competition where every player faces every other player.",
        ];

        $template = $templates[$type] ?? $templates['tournament'];
        return str_replace('{sport}', ucfirst($sport), $template);
    }

    /**
     * Get appropriate format for competition type
     */
    private function getFormatForType(string $type): string
    {
        $formatMap = [
            'tournament' => fake()->randomElement(['single_elimination', 'double_elimination', 'swiss']),
            'league' => 'league',
            'single_match' => 'single_elimination',
            'ladder' => 'ladder',
            'round_robin' => 'round_robin',
        ];

        return $formatMap[$type] ?? 'single_elimination';
    }

    /**
     * Get max participants for type
     */
    private function getMaxParticipantsForType(string $type): ?int
    {
        $limits = [
            'tournament' => [8, 16, 32, 64],
            'league' => [10, 12, 16, 20],
            'single_match' => 2,
            'ladder' => [20, 30, 50],
            'round_robin' => [6, 8, 10, 12],
        ];

        $limit = $limits[$type] ?? [16, 32];
        return is_array($limit) ? fake()->randomElement($limit) : $limit;
    }

    /**
     * Get min participants for type
     */
    private function getMinParticipantsForType(string $type): int
    {
        $mins = [
            'tournament' => 4,
            'league' => 6,
            'single_match' => 2,
            'ladder' => 4,
            'round_robin' => 4,
        ];

        return $mins[$type] ?? 4;
    }

    /**
     * Get duration in days for type
     */
    private function getDurationForType(string $type): int
    {
        $durations = [
            'tournament' => [1, 2, 3, 7], // 1-7 days
            'league' => [30, 60, 90], // 1-3 months
            'single_match' => 1,
            'ladder' => [60, 90, 120], // 2-4 months
            'round_robin' => [7, 14, 21], // 1-3 weeks
        ];

        $duration = $durations[$type] ?? [7];
        return is_array($duration) ? fake()->randomElement($duration) : $duration;
    }

    /**
     * Get total rounds for type
     */
    private function getTotalRoundsForType(string $type): ?int
    {
        switch ($type) {
            case 'tournament':
                return fake()->numberBetween(3, 6); // Depends on participants
            case 'league':
                return fake()->numberBetween(10, 20);
            case 'round_robin':
                return 1; // One round where everyone plays everyone
            case 'ladder':
                return null; // Ongoing
            default:
                return 1;
        }
    }

    /**
     * Generate prize structure
     */
    private function generatePrizeStructure(): array
    {
        if (fake()->boolean(40)) { // 40% chance of having prizes
            return [
                '1st' => fake()->randomElement(['$500', '$300', '$200', 'Trophy + $100']),
                '2nd' => fake()->randomElement(['$200', '$150', '$100', 'Trophy']),
                '3rd' => fake()->randomElement(['$100', '$75', '$50', 'Medal']),
            ];
        }

        return [];
    }

    /**
     * Generate competition rules
     */
    private function generateRules(string $type, string $sport): array
    {
        $baseRules = [
            'scoring' => fake()->randomElement(['best_of_3', 'best_of_5', 'first_to_11', 'first_to_15']),
            'time_limit' => fake()->randomElement(['60_minutes', '90_minutes', '120_minutes']),
            'late_policy' => 'Players must arrive 15 minutes before match time',
        ];

        if ($sport === 'squash') {
            $baseRules['serving'] = 'Traditional squash serving rules apply';
            $baseRules['let_rules'] = 'Standard WSF let rules';
        }

        return $baseRules;
    }

    /**
     * Generate competition settings
     */
    private function generateSettings(string $type): array
    {
        return [
            'allow_substitutions' => fake()->boolean(20),
            'require_confirmation' => fake()->boolean(80),
            'auto_advance_winners' => fake()->boolean(60),
            'send_notifications' => fake()->boolean(90),
            'track_detailed_stats' => fake()->boolean(70),
        ];
    }

    /**
     * State for tournament competitions
     */
    public function tournament(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'tournament',
                'format' => fake()->randomElement(['single_elimination', 'double_elimination']),
                'max_participants' => fake()->randomElement([8, 16, 32]),
                'total_rounds' => fake()->numberBetween(3, 5),
            ];
        });
    }

    /**
     * State for league competitions
     */
    public function league(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'league',
                'format' => 'league',
                'max_participants' => fake()->randomElement([10, 12, 16]),
                'total_rounds' => fake()->numberBetween(10, 20),
            ];
        });
    }

    /**
     * State for ongoing competitions
     */
    public function ongoing(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'in_progress',
                'registration_start' => now()->subDays(45),
                'registration_end' => now()->subDays(15),
                'start_date' => now()->subDays(7),
                'rounds_completed' => fake()->numberBetween(1, 5),
            ];
        });
    }

    /**
     * State for upcoming competitions
     */
    public function upcoming(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'open',
                'registration_start' => now(),
                'registration_end' => now()->addDays(14),
                'start_date' => now()->addDays(21),
                'rounds_completed' => 0,
            ];
        });
    }

    /**
     * State for completed competitions
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'registration_start' => now()->subDays(60),
                'registration_end' => now()->subDays(30),
                'start_date' => now()->subDays(21),
                'end_date' => now()->subDays(7),
                'rounds_completed' => $attributes['total_rounds'] ?? 5,
            ];
        });
    }

    /**
     * State for free competitions
     */
    public function free(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'entry_fee' => null,
                'prize_structure' => ['1st' => 'Trophy', '2nd' => 'Medal', '3rd' => 'Certificate'],
            ];
        });
    }

    /**
     * State for ranked competitions
     */
    public function ranked(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_ranked' => true,
                'requirements' => 'Minimum skill rating required for participation',
                'settings' => array_merge($attributes['settings'] ?? [], [
                    'track_detailed_stats' => true,
                    'update_player_rankings' => true,
                ]),
            ];
        });
    }
}
