<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Round;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Round>
 */
class RoundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roundNumber = fake()->numberBetween(1, 5);
        $isElimination = fake()->boolean(30); // 30% chance of being elimination round
        $statuses = ['pending', 'active', 'completed'];
        $status = fake()->randomElement($statuses);

        return [
            'competition_id' => Competition::factory(),
            'name' => $this->generateRoundName($roundNumber, $isElimination),
            'round_number' => $roundNumber,
            'description' => fake()->optional(0.7)->paragraph(),
            'status' => $status,
            'start_date' => $status !== 'pending' ? fake()->dateTimeBetween('-1 month', '+1 month') : fake()->dateTimeBetween('+1 day', '+2 months'),
            'end_date' => $status === 'completed' ? fake()->dateTimeBetween('-1 month', '+1 month') : fake()->optional(0.6)->dateTimeBetween('+1 week', '+3 months'),
            'total_groups' => fake()->numberBetween(2, 8),
            'settings' => $this->generateRoundSettings($isElimination),
            'is_elimination_round' => $isElimination,
            'players_advance' => $isElimination ? fake()->numberBetween(1, 3) : null,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Generate a realistic round name
     */
    private function generateRoundName(int $roundNumber, bool $isElimination): string
    {
        if ($isElimination) {
            $eliminationNames = [
                1 => 'Preliminary Round',
                2 => 'Round of 32',
                3 => 'Round of 16',
                4 => 'Quarter Finals',
                5 => 'Semi Finals'
            ];
            return $eliminationNames[$roundNumber] ?? "Round $roundNumber";
        }

        $groupNames = [
            1 => 'Group Stage',
            2 => 'Second Round',
            3 => 'Third Round',
            4 => 'Fourth Round',
            5 => 'Final Round'
        ];

        return $groupNames[$roundNumber] ?? "Round $roundNumber";
    }

    /**
     * Generate round-specific settings
     */
    private function generateRoundSettings(bool $isElimination): array
    {
        $baseSettings = [
            'allow_ties' => fake()->boolean(70),
            'time_limit' => fake()->optional(0.6)->numberBetween(30, 120),
            'scoring_system' => fake()->randomElement(['best_of_3', 'best_of_5', 'first_to_11', 'first_to_15']),
        ];

        if ($isElimination) {
            $baseSettings['advancement_criteria'] = fake()->randomElement(['top_n', 'winner_only', 'top_percentage']);
            $baseSettings['tiebreaker_rules'] = fake()->randomElement(['head_to_head', 'point_difference', 'games_won']);
        } else {
            $baseSettings['round_robin'] = true;
            $baseSettings['all_play_all'] = fake()->boolean(80);
        }

        return $baseSettings;
    }

    /**
     * Create a pending round
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
            'start_date' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'end_date' => null,
        ]);
    }

    /**
     * Create an active round
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
     * Create a completed round
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
     * Create an elimination round
     */
    public function elimination(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_elimination_round' => true,
            'players_advance' => fake()->numberBetween(1, 2),
            'name' => fake()->randomElement(['Quarter Finals', 'Semi Finals', 'Finals', 'Round of 16']),
            'settings' => array_merge($attributes['settings'] ?? [], [
                'advancement_criteria' => 'winner_only',
                'single_elimination' => true,
            ]),
        ]);
    }

    /**
     * Create a group stage round
     */
    public function groupStage(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_elimination_round' => false,
            'players_advance' => null,
            'name' => 'Group Stage',
            'total_groups' => fake()->numberBetween(4, 8),
            'settings' => array_merge($attributes['settings'] ?? [], [
                'round_robin' => true,
                'all_play_all' => true,
            ]),
        ]);
    }

    /**
     * Create first round
     */
    public function firstRound(): static
    {
        return $this->state(fn(array $attributes) => [
            'round_number' => 1,
            'name' => 'First Round',
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
}
