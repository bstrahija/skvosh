<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Round;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all competitions that could have rounds
        $competitions = Competition::whereIn('format', ['single_elimination', 'double_elimination', 'swiss'])
            ->where('status', '!=', 'draft')
            ->get();

        foreach ($competitions as $competition) {
            $this->createRoundsForCompetition($competition);
        }

        // Create some additional rounds for testing
        $this->createTestRounds();
    }

    /**
     * Create rounds for a specific competition based on its format
     */
    private function createRoundsForCompetition(Competition $competition): void
    {
        // Skip if rounds already exist for this competition
        if ($competition->rounds()->count() > 0) {
            return;
        }

        $participantCount = $competition->participants()->count();

        if ($participantCount < 4) {
            return; // Not enough participants for multiple rounds
        }

        switch ($competition->format) {
            case 'single_elimination':
                $this->createEliminationRounds($competition, $participantCount);
                break;
            case 'double_elimination':
                $this->createDoubleEliminationRounds($competition, $participantCount);
                break;
            case 'swiss':
                $this->createSwissRounds($competition, $participantCount);
                break;
        }
    }

    /**
     * Create single elimination tournament rounds
     */
    private function createEliminationRounds(Competition $competition, int $participantCount): void
    {
        $roundsNeeded = $this->calculateEliminationRounds($participantCount);
        $roundNames = $this->getEliminationRoundNames($roundsNeeded);

        for ($i = 1; $i <= $roundsNeeded; $i++) {
            $isCompleted = fake()->boolean($competition->status === 'completed' ? 80 : 20);
            $status = $this->determineRoundStatus($i, $roundsNeeded, $competition->status, $isCompleted);

            Round::create([
                'competition_id' => $competition->id,
                'name' => $roundNames[$i - 1] ?? "Round $i",
                'round_number' => $i,
                'description' => "Round $i of {$competition->name}",
                'status' => $status,
                'start_date' => $this->getRoundStartDate($i, $competition),
                'end_date' => $status === 'completed' ? fake()->dateTimeBetween('-1 week', 'now') : null,
                'total_groups' => $this->getGroupsForRound($participantCount, $i),
                'settings' => [
                    'single_elimination' => true,
                    'advancement_criteria' => 'winner_only',
                    'scoring_system' => 'best_of_5',
                ],
                'is_elimination_round' => true,
                'players_advance' => $i < $roundsNeeded ? 1 : null, // Winners advance except in final
                'notes' => fake()->optional(0.3)->sentence(),
            ]);
        }
    }

    /**
     * Create double elimination tournament rounds
     */
    private function createDoubleEliminationRounds(Competition $competition, int $participantCount): void
    {
        $mainRounds = $this->calculateEliminationRounds($participantCount);

        // Winners bracket rounds
        for ($i = 1; $i <= $mainRounds; $i++) {
            $status = $this->determineRoundStatus($i, $mainRounds, $competition->status, fake()->boolean(60));

            Round::create([
                'competition_id' => $competition->id,
                'name' => "Winners Round $i",
                'round_number' => $i,
                'description' => "Winners bracket round $i",
                'status' => $status,
                'start_date' => $this->getRoundStartDate($i, $competition),
                'end_date' => $status === 'completed' ? fake()->dateTimeBetween('-1 week', 'now') : null,
                'total_groups' => $this->getGroupsForRound($participantCount, $i),
                'settings' => [
                    'bracket_type' => 'winners',
                    'advancement_criteria' => 'winner_only',
                    'scoring_system' => 'best_of_5',
                ],
                'is_elimination_round' => true,
                'players_advance' => 1,
            ]);
        }

        // Losers bracket rounds (more complex, simplified here)
        for ($i = 1; $i <= $mainRounds - 1; $i++) {
            Round::create([
                'competition_id' => $competition->id,
                'name' => "Losers Round $i",
                'round_number' => $mainRounds + $i,
                'description' => "Losers bracket round $i",
                'status' => 'pending',
                'total_groups' => max(1, intval($this->getGroupsForRound($participantCount, $i) / 2)),
                'settings' => [
                    'bracket_type' => 'losers',
                    'advancement_criteria' => 'winner_only',
                    'scoring_system' => 'best_of_5',
                ],
                'is_elimination_round' => true,
                'players_advance' => 1,
            ]);
        }
    }

    /**
     * Create Swiss tournament rounds
     */
    private function createSwissRounds(Competition $competition, int $participantCount): void
    {
        $roundsNeeded = min(8, ceil(log($participantCount, 2))); // Swiss typically 5-8 rounds

        for ($i = 1; $i <= $roundsNeeded; $i++) {
            $status = $this->determineRoundStatus($i, $roundsNeeded, $competition->status, fake()->boolean(40));

            Round::create([
                'competition_id' => $competition->id,
                'name' => "Swiss Round $i",
                'round_number' => $i,
                'description' => "Swiss system round $i",
                'status' => $status,
                'start_date' => $this->getRoundStartDate($i, $competition),
                'end_date' => $status === 'completed' ? fake()->dateTimeBetween('-1 week', 'now') : null,
                'total_groups' => intval($participantCount / 2), // Pairs for Swiss
                'settings' => [
                    'swiss_system' => true,
                    'pairing_method' => 'score_based',
                    'scoring_system' => 'best_of_3',
                ],
                'is_elimination_round' => false,
                'players_advance' => null,
            ]);
        }
    }

    /**
     * Create additional test rounds for demonstration
     */
    private function createTestRounds(): void
    {
        // Get a few competitions that don't already have rounds
        $testCompetitions = Competition::whereDoesntHave('rounds')->limit(3)->get();

        foreach ($testCompetitions as $competition) {
            // Create a group stage round
            Round::factory()
                ->forCompetition($competition)
                ->groupStage()
                ->completed()
                ->create([
                    'round_number' => 1,
                ]);

            // Create an elimination round
            Round::factory()
                ->forCompetition($competition)
                ->elimination()
                ->active()
                ->create([
                    'round_number' => 2,
                ]);
        }
    }

    /**
     * Calculate number of elimination rounds needed
     */
    private function calculateEliminationRounds(int $participantCount): int
    {
        return ceil(log($participantCount, 2));
    }

    /**
     * Get elimination round names
     */
    private function getEliminationRoundNames(int $totalRounds): array
    {
        $names = [];

        for ($i = $totalRounds; $i >= 1; $i--) {
            $playersInRound = pow(2, $i);

            if ($i === 1) {
                $names[] = 'Finals';
            } elseif ($i === 2) {
                $names[] = 'Semi Finals';
            } elseif ($i === 3) {
                $names[] = 'Quarter Finals';
            } elseif ($playersInRound <= 32) {
                $names[] = "Round of $playersInRound";
            } else {
                $names[] = "Round " . ($totalRounds - $i + 1);
            }
        }

        return array_reverse($names);
    }

    /**
     * Determine round status based on competition status and round position
     */
    private function determineRoundStatus(int $roundNumber, int $totalRounds, string $competitionStatus, bool $isCompleted): string
    {
        if ($competitionStatus === 'completed' && $isCompleted) {
            return 'completed';
        }

        if ($competitionStatus === 'in_progress') {
            if ($roundNumber === 1 || ($roundNumber <= 2 && fake()->boolean(70))) {
                return fake()->randomElement(['active', 'completed']);
            }
        }

        return 'pending';
    }

    /**
     * Get round start date relative to competition
     */
    private function getRoundStartDate(int $roundNumber, Competition $competition): ?\DateTime
    {
        if (!$competition->start_date) {
            return null;
        }

        $baseDate = $competition->start_date;
        $daysToAdd = ($roundNumber - 1) * fake()->numberBetween(3, 7); // Each round 3-7 days apart

        return $baseDate->addDays($daysToAdd);
    }

    /**
     * Calculate number of groups needed for a round
     */
    private function getGroupsForRound(int $participantCount, int $roundNumber): int
    {
        $playersInRound = intval($participantCount / pow(2, $roundNumber - 1));
        return max(1, intval($playersInRound / 4)); // Roughly 4 players per group
    }
}
