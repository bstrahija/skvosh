<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clubs = Club::all();
        $users = User::all();

        if ($clubs->isEmpty() || $users->isEmpty()) {
            $this->command->info('No clubs or users found. Please run ClubSeeder first.');
            return;
        }

        $this->command->info('Creating competitions...');

        // Create a variety of competitions for each club
        foreach ($clubs as $club) {
            $this->createCompetitionsForClub($club, $users);
        }

        $this->command->info('Competitions created successfully!');
    }

    /**
     * Create competitions for a specific club
     */
    private function createCompetitionsForClub(Club $club, $users): void
    {
        $clubMembers = $club->users;
        $competitionCount = rand(2, 6); // 2-6 competitions per club

        for ($i = 0; $i < $competitionCount; $i++) {
            $creator = $clubMembers->isNotEmpty() ? $clubMembers->random() : $users->random();
            $competition = $this->createCompetition($club, $creator);

            if ($competition) {
                $this->addParticipantsToCompetition($competition, $clubMembers, $users);
            }
        }
    }

    /**
     * Create a single competition
     */
    private function createCompetition(Club $club, User $creator): ?Competition
    {
        // Determine competition type and create appropriate competition
        $competitionType = fake()->randomElement([
            'upcoming',
            'ongoing',
            'completed',
            'tournament',
            'league'
        ]);

        $factoryMethod = match ($competitionType) {
            'upcoming' => 'upcoming',
            'ongoing' => 'ongoing',
            'completed' => 'completed',
            'tournament' => 'tournament',
            'league' => 'league',
            default => null
        };

        if (!$factoryMethod) {
            return null;
        }

        // Create competition with appropriate state
        $competition = Competition::factory()
            ->$factoryMethod()
            ->create([
                'club_id' => $club->id,
                'created_by' => $creator->id,
                'sport' => $this->getClubSport($club),
            ]);

        // Add creator as admin
        $competition->addAdmin($creator);

        return $competition;
    }

    /**
     * Add participants to competition
     */
    private function addParticipantsToCompetition(Competition $competition, $clubMembers, $allUsers): void
    {
        $maxParticipants = $competition->max_participants ?? 16;
        $minParticipants = $competition->min_participants;

        // Determine how many participants to add
        if ($competition->status === 'completed') {
            $participantCount = $maxParticipants; // Full participation for completed
        } elseif ($competition->status === 'in_progress') {
            $participantCount = rand($minParticipants, $maxParticipants);
        } else {
            $participantCount = rand($minParticipants, min($maxParticipants, $minParticipants + 5));
        }

        // Prefer club members but include some external participants
        $participants = collect();

        // Add club members first (70% of participants)
        $clubMemberCount = min(intval($participantCount * 0.7), $clubMembers->count());
        if ($clubMemberCount > 0) {
            $selectedClubMembers = $clubMembers->random($clubMemberCount);
            $participants = $participants->merge($selectedClubMembers);
        }

        // Fill remaining spots with any users
        $remainingSpots = $participantCount - $participants->count();
        if ($remainingSpots > 0) {
            $availableUsers = $allUsers->diff($participants);
            if ($availableUsers->count() >= $remainingSpots) {
                $additionalUsers = $availableUsers->random($remainingSpots);
                $participants = $participants->merge($additionalUsers);
            }
        }

        // Add participants with appropriate data
        foreach ($participants as $index => $user) {
            // Skip if user is already the creator/admin
            if ($user->id === $competition->created_by) {
                continue;
            }

            $this->addParticipant($competition, $user, $index + 1);
        }

        // Add 1-2 additional admins for larger competitions
        if ($participantCount > 10 && $clubMembers->count() > 3) {
            $potentialAdmins = $clubMembers->diff([$competition->creator])->random(min(2, $clubMembers->count() - 1));
            foreach ($potentialAdmins as $admin) {
                if (rand(1, 100) <= 30) { // 30% chance to make them admin
                    $competition->addAdmin($admin);
                }
            }
        }
    }

    /**
     * Add a single participant to competition
     */
    private function addParticipant(Competition $competition, User $user, int $seed): void
    {
        $status = $this->getParticipantStatus($competition);
        $points = $wins = $losses = $draws = 0;
        $ranking = null;

        // Generate realistic stats for completed competitions
        if ($competition->status === 'completed') {
            $matches = rand(3, 8);
            $wins = rand(0, $matches);
            $losses = $matches - $wins;
            $points = ($wins * 3) + ($draws * 1); // 3 points for win, 1 for draw
            $ranking = $seed; // Use seed as final ranking for simplicity
        } elseif ($competition->status === 'in_progress') {
            // Some progress for ongoing competitions
            $matches = rand(0, 4);
            $wins = rand(0, $matches);
            $losses = $matches - $wins;
            $points = ($wins * 3) + ($draws * 1);
        }

        $pivotData = [
            'role' => 'participant',
            'status' => $status,
            'seed' => $seed,
            'current_ranking' => $ranking,
            'points' => $points,
            'wins' => $wins,
            'losses' => $losses,
            'draws' => $draws,
            'statistics' => json_encode($this->generatePlayerStatistics($wins, $losses)),
            'entry_fee_paid' => $competition->entry_fee,
            'fee_paid' => $competition->entry_fee ? fake()->boolean(90) : true, // 90% paid if there's a fee
            'registered_at' => $competition->registration_start?->addDays(rand(0, 7)),
            'confirmed_at' => $status === 'confirmed' ? $competition->registration_start?->addDays(rand(1, 10)) : null,
            'notes' => fake()->optional(0.1)->sentence(),
        ];

        $competition->participants()->attach($user->id, $pivotData);
    }

    /**
     * Get participant status based on competition status
     */
    private function getParticipantStatus(Competition $competition): string
    {
        switch ($competition->status) {
            case 'draft':
            case 'open':
                return fake()->randomElement(['registered', 'confirmed', 'confirmed', 'confirmed']); // Mostly confirmed
            case 'in_progress':
            case 'completed':
                return fake()->randomElement(['confirmed', 'confirmed', 'confirmed', 'eliminated']); // Mostly confirmed, some eliminated
            default:
                return 'registered';
        }
    }

    /**
     * Generate player statistics
     */
    private function generatePlayerStatistics(int $wins, int $losses): array
    {
        $totalMatches = $wins + $losses;

        if ($totalMatches === 0) {
            return [];
        }

        return [
            'games_won' => $wins * rand(2, 3) + $losses * rand(0, 1), // Approximate games won
            'games_lost' => $losses * rand(2, 3) + $wins * rand(0, 1), // Approximate games lost
            'average_match_duration' => rand(30, 90), // Minutes
            'longest_match' => rand(60, 120), // Minutes
            'shortest_match' => rand(15, 45), // Minutes
        ];
    }

    /**
     * Get a sport that the club supports
     */
    private function getClubSport(Club $club): string
    {
        $clubSports = $club->sports ?? ['squash'];
        return fake()->randomElement($clubSports);
    }
}
