<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Group;
use App\Models\Round;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all rounds that need groups
        $rounds = Round::where('total_groups', '>', 0)->get();

        foreach ($rounds as $round) {
            $this->createGroupsForRound($round);
        }

        // Create some additional test groups
        $this->createTestGroups();
    }

    /**
     * Create groups for a specific round
     */
    private function createGroupsForRound(Round $round): void
    {
        // Skip if groups already exist for this round
        if ($round->groups()->count() > 0) {
            return;
        }

        $groupLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        for ($i = 1; $i <= $round->total_groups; $i++) {
            $groupLetter = $groupLetters[$i - 1] ?? $i;
            $maxPlayers = $this->determineMaxPlayers($round, $i);

            $group = Group::create([
                'competition_id' => $round->competition_id,
                'round_id' => $round->id,
                'name' => "Group $groupLetter",
                'group_number' => $i,
                'description' => "Group $groupLetter for {$round->name}",
                'status' => $this->determineGroupStatus($round->status),
                'max_players' => $maxPlayers,
                'standings' => $this->generateGroupStandings($maxPlayers, $round->status),
                'settings' => $this->generateGroupSettings($round),
                'start_date' => $round->start_date,
                'end_date' => $round->status === 'completed' ? $round->end_date : null,
                'notes' => fake()->optional(0.2)->sentence(),
            ]);

            // Add participants to the group
            $this->addParticipantsToGroup($group, $round);
        }
    }

    /**
     * Determine max players for a group based on round characteristics
     */
    private function determineMaxPlayers(Round $round, int $groupNumber): int
    {
        $competition = $round->competition;
        $totalParticipants = $competition->participants()->count();

        if ($totalParticipants === 0) {
            return fake()->numberBetween(3, 6);
        }

        // Distribute participants roughly evenly across groups
        $averagePerGroup = intval($totalParticipants / $round->total_groups);

        // Add some variation
        return max(2, $averagePerGroup + fake()->numberBetween(-1, 2));
    }

    /**
     * Determine group status based on round status
     */
    private function determineGroupStatus(string $roundStatus): string
    {
        switch ($roundStatus) {
            case 'completed':
                return fake()->randomElement(['completed', 'completed', 'completed', 'active']); // Mostly completed
            case 'active':
                return fake()->randomElement(['active', 'active', 'completed', 'pending']); // Mix of statuses
            case 'pending':
            default:
                return 'pending';
        }
    }

    /**
     * Generate group standings based on status
     */
    private function generateGroupStandings(int $maxPlayers, string $roundStatus): array
    {
        if ($roundStatus === 'pending') {
            return [];
        }

        $playerCount = fake()->numberBetween(2, $maxPlayers);
        $standings = [];

        for ($i = 1; $i <= $playerCount; $i++) {
            $matchesPlayed = $roundStatus === 'completed'
                ? fake()->numberBetween(3, 8)
                : fake()->numberBetween(0, 5);

            $wins = $matchesPlayed > 0 ? fake()->numberBetween(0, $matchesPlayed) : 0;
            $losses = $matchesPlayed - $wins;

            // Generate realistic scores
            $pointsFor = $this->generatePoints($wins, $losses, true);
            $pointsAgainst = $this->generatePoints($losses, $wins, false);

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

        // Sort standings properly
        usort($standings, function ($a, $b) {
            if ($a['wins'] === $b['wins']) {
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
     * Generate realistic points based on wins/losses
     */
    private function generatePoints(int $wins, int $losses, bool $isFor): int
    {
        if ($wins === 0 && $losses === 0) {
            return 0;
        }

        $basePoints = $isFor ? $wins * fake()->numberBetween(8, 15) : $losses * fake()->numberBetween(5, 12);
        $variation = fake()->numberBetween(-3, 8);

        return max(0, $basePoints + $variation);
    }

    /**
     * Generate group settings based on round
     */
    private function generateGroupSettings(Round $round): array
    {
        $baseSettings = [
            'match_format' => fake()->randomElement(['best_of_3', 'best_of_5', 'first_to_11']),
            'point_system' => 'standard',
            'time_limits' => [
                'match_time' => fake()->numberBetween(45, 90),
                'warm_up_time' => fake()->numberBetween(5, 10),
            ],
        ];

        if ($round->is_elimination_round) {
            $baseSettings['advancement_count'] = $round->players_advance ?? 1;
            $baseSettings['elimination_criteria'] = 'bottom_n';
        } else {
            $baseSettings['round_robin'] = true;
            $baseSettings['all_play_all'] = true;
        }

        return $baseSettings;
    }

    /**
     * Add participants to a group
     */
    private function addParticipantsToGroup(Group $group, Round $round): void
    {
        $competition = $round->competition;
        $participants = $competition->participants()->limit($group->max_players)->get();

        if ($participants->isEmpty()) {
            // If no real participants, we'll skip adding fake ones for now
            return;
        }

        foreach ($participants as $index => $participant) {
            $wins = fake()->numberBetween(0, 3);
            $losses = fake()->numberBetween(0, 3);
            $draws = fake()->numberBetween(0, 1);

            $statistics = [
                'matches_played' => $wins + $losses + $draws,
                'total_points' => fake()->numberBetween(0, 50),
                'avg_points_per_match' => fake()->randomFloat(2, 5, 15),
                'consistency_rating' => fake()->numberBetween(1, 10),
            ];

            $group->participants()->attach($participant->id, [
                'position' => $index + 1,
                'seed' => fake()->randomFloat(2, 1, $group->max_players),
                'points' => fake()->numberBetween(0, 15),
                'wins' => $wins,
                'losses' => $losses,
                'draws' => $draws,
                'statistics' => json_encode($statistics),
                'advanced' => false,
                'eliminated' => false,
                'joined_at' => $round->start_date ?? now(),
                'notes' => fake()->optional(0.2)->sentence(),
            ]);
        }

        // Update group standings after adding participants
        $group->updateStandings();
    }

    /**
     * Create additional test groups for demonstration
     */
    private function createTestGroups(): void
    {
        // Get some competitions and rounds for testing that don't have groups yet
        $competitions = Competition::with('rounds')->limit(2)->get();

        foreach ($competitions as $competition) {
            $availableRounds = $competition->rounds()->whereDoesntHave('groups')->get();

            if ($availableRounds->isEmpty()) {
                // Create a test round first if no rounds exist without groups
                $round = Round::factory()
                    ->forCompetition($competition)
                    ->groupStage()
                    ->active()
                    ->create([
                        'total_groups' => 2,
                        'round_number' => $competition->rounds()->max('round_number') + 1 ?? 1,
                    ]);
            } else {
                $round = $availableRounds->first();
            }

            // Create test groups with different characteristics
            if ($round->groups()->count() === 0) {
                Group::factory()
                    ->forRound($round)
                    ->withLetter('A')
                    ->active()
                    ->small()
                    ->create([
                        'group_number' => 1,
                    ]);

                Group::factory()
                    ->forRound($round)
                    ->withLetter('B')
                    ->completed()
                    ->large()
                    ->create([
                        'group_number' => 2,
                    ]);
            }
        }
    }
}
