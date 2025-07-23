<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Group;
use App\Models\Round;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $groupNumber = fake()->numberBetween(1, 8);
        $groupName = $this->generateGroupName($groupNumber);
        $statuses = ['pending', 'active', 'completed'];
        $status = fake()->randomElement($statuses);

        return [
            'competition_id' => Competition::factory(),
            'round_id' => Round::factory(),
            'name' => $groupName,
            'group_number' => $groupNumber,
            'description' => fake()->optional(0.4)->sentence(),
            'status' => $status,
            'max_players' => fake()->numberBetween(3, 6),
            'standings' => $this->generateStandings($status),
            'settings' => $this->generateGroupSettings(),
            'start_date' => $status !== 'pending' ? fake()->dateTimeBetween('-1 month', '+1 month') : fake()->dateTimeBetween('+1 day', '+2 months'),
            'end_date' => $status === 'completed' ? fake()->dateTimeBetween('-1 month', '+1 month') : fake()->optional(0.6)->dateTimeBetween('+1 week', '+3 months'),
            'notes' => fake()->optional(0.2)->sentence(),
        ];
    }

    /**
     * Generate a group name based on the number
     */
    private function generateGroupName(int $groupNumber): string
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        if ($groupNumber <= 8) {
            return 'Group ' . $letters[$groupNumber - 1];
        }

        return "Group $groupNumber";
    }

    /**
     * Generate realistic group standings
     */
    private function generateStandings(string $status): array
    {
        if ($status === 'pending') {
            return [];
        }

        $playerCount = fake()->numberBetween(3, 6);
        $standings = [];

        for ($i = 1; $i <= $playerCount; $i++) {
            $matchesPlayed = $status === 'completed' ? fake()->numberBetween(3, 8) : fake()->numberBetween(0, 5);
            $wins = $matchesPlayed > 0 ? fake()->numberBetween(0, $matchesPlayed) : 0;
            $losses = $matchesPlayed - $wins;
            $pointsFor = $matchesPlayed > 0 ? fake()->numberBetween($wins * 5, $wins * 15 + 10) : 0;
            $pointsAgainst = $matchesPlayed > 0 ? fake()->numberBetween($losses * 3, $losses * 12 + 8) : 0;

            $standings[] = [
                'user_id' => fake()->numberBetween(1, 100),
                'name' => fake()->name(),
                'position' => $i,
                'matches_played' => $matchesPlayed,
                'wins' => $wins,
                'losses' => $losses,
                'points_for' => $pointsFor,
                'points_against' => $pointsAgainst,
                'point_difference' => $pointsFor - $pointsAgainst,
                'win_percentage' => $matchesPlayed > 0 ? round($wins / $matchesPlayed * 100, 2) : 0,
            ];
        }

        // Sort by wins desc, then by point difference desc
        usort($standings, function ($a, $b) {
            if ($a['wins'] == $b['wins']) {
                return $b['point_difference'] <=> $a['point_difference'];
            }
            return $b['wins'] <=> $a['wins'];
        });

        // Update positions after sorting
        foreach ($standings as $index => &$standing) {
            $standing['position'] = $index + 1;
        }

        return $standings;
    }

    /**
     * Generate group-specific settings
     */
    private function generateGroupSettings(): array
    {
        return [
            'match_format' => fake()->randomElement(['best_of_3', 'best_of_5', 'first_to_11']),
            'round_robin' => fake()->boolean(80),
            'double_round_robin' => fake()->boolean(20),
            'point_system' => fake()->randomElement(['standard', 'tennis', 'custom']),
            'tiebreaker' => fake()->randomElement(['head_to_head', 'point_difference', 'sets_won']),
            'time_limits' => [
                'match_time' => fake()->numberBetween(30, 90),
                'warm_up_time' => fake()->numberBetween(3, 10),
            ],
        ];
    }

    /**
     * Create a pending group
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
            'standings' => [],
            'start_date' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'end_date' => null,
        ]);
    }

    /**
     * Create an active group
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'active',
            'start_date' => fake()->dateTimeBetween('-1 week', 'now'),
            'end_date' => fake()->dateTimeBetween('+1 week', '+1 month'),
        ]);
    }

    /**
     * Create a completed group
     */
    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
            'start_date' => fake()->dateTimeBetween('-2 months', '-1 week'),
            'end_date' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Create a group with specific letter name
     */
    public function withLetter(string $letter): static
    {
        return $this->state(fn(array $attributes) => [
            'name' => "Group $letter",
        ]);
    }

    /**
     * Create for a specific round
     */
    public function forRound(Round $round): static
    {
        return $this->state(fn(array $attributes) => [
            'competition_id' => $round->competition_id,
            'round_id' => $round->id,
        ]);
    }

    /**
     * Create for a specific competition
     */
    public function forCompetition(Competition $competition): static
    {
        return $this->state(fn(array $attributes) => [
            'competition_id' => $competition->id,
        ]);
    }

    /**
     * Create with a specific number of max players
     */
    public function withMaxPlayers(int $maxPlayers): static
    {
        return $this->state(fn(array $attributes) => [
            'max_players' => $maxPlayers,
        ]);
    }

    /**
     * Create small groups (3-4 players)
     */
    public function small(): static
    {
        return $this->state(fn(array $attributes) => [
            'max_players' => fake()->numberBetween(3, 4),
        ]);
    }

    /**
     * Create large groups (5-8 players)
     */
    public function large(): static
    {
        return $this->state(fn(array $attributes) => [
            'max_players' => fake()->numberBetween(5, 8),
        ]);
    }
}
