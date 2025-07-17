<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courts = Court::with('club')->get();
        $users = User::all();

        if ($courts->isEmpty() || $users->isEmpty()) {
            $this->command->info('No courts or users found. Please run ClubSeeder and CourtSeeder first.');
            return;
        }

        $this->command->info('Creating reservations...');

        // Create reservations for the past 30 days (completed)
        $this->createPastReservations($courts, $users);

        // Create reservations for today
        $this->createTodayReservations($courts, $users);

        // Create future reservations (next 30 days)
        $this->createFutureReservations($courts, $users);

        $this->command->info('Reservations created successfully!');
    }

    /**
     * Create past reservations (completed)
     */
    private function createPastReservations($courts, $users): void
    {
        $pastDays = 30;

        for ($i = $pastDays; $i > 0; $i--) {
            $date = Carbon::now()->subDays($i);

            // Skip creating reservations for very old dates
            if ($date->isWeekend()) {
                $reservationsPerDay = rand(15, 25); // More on weekends
            } else {
                $reservationsPerDay = rand(8, 15); // Fewer on weekdays
            }

            for ($j = 0; $j < $reservationsPerDay; $j++) {
                $court = $courts->random();
                $user = $this->getRandomClubMember($court->club, $users);

                $this->createReservation($court, $user, $date, 'completed');
            }
        }
    }

    /**
     * Create today's reservations
     */
    private function createTodayReservations($courts, $users): void
    {
        $today = Carbon::now();
        $reservationsToday = rand(10, 20);

        for ($i = 0; $i < $reservationsToday; $i++) {
            $court = $courts->random();
            $user = $this->getRandomClubMember($court->club, $users);

            $status = rand(1, 100) <= 80 ? 'confirmed' : 'pending'; // 80% confirmed
            $this->createReservation($court, $user, $today, $status);
        }
    }

    /**
     * Create future reservations
     */
    private function createFutureReservations($courts, $users): void
    {
        $futureDays = 30;

        for ($i = 1; $i <= $futureDays; $i++) {
            $date = Carbon::now()->addDays($i);

            if ($date->isWeekend()) {
                $reservationsPerDay = rand(12, 20); // More on weekends
            } else {
                $reservationsPerDay = rand(6, 12); // Fewer on weekdays
            }

            for ($j = 0; $j < $reservationsPerDay; $j++) {
                $court = $courts->random();
                $user = $this->getRandomClubMember($court->club, $users);

                // Most future reservations are confirmed or pending
                $status = fake()->randomElement(['confirmed', 'pending', 'confirmed', 'confirmed']);
                $this->createReservation($court, $user, $date, $status);
            }
        }

        // Create some cancelled reservations
        $cancelledCount = rand(5, 15);
        for ($i = 0; $i < $cancelledCount; $i++) {
            $court = $courts->random();
            $user = $this->getRandomClubMember($court->club, $users);
            $date = Carbon::now()->addDays(rand(1, $futureDays));

            $this->createReservation($court, $user, $date, 'cancelled');
        }
    }

    /**
     * Create a single reservation
     */
    private function createReservation(Court $court, User $user, Carbon $date, string $status): void
    {
        $timeSlots = $this->generateAvailableTimeSlots($court, $date);

        if (empty($timeSlots)) {
            return; // No available slots
        }

        $selectedSlot = fake()->randomElement($timeSlots);

        $reservationData = [
            'court_id' => $court->id,
            'user_id' => $user->id,
            'reservation_date' => $date->format('Y-m-d'),
            'start_time' => $selectedSlot['start'],
            'end_time' => $selectedSlot['end'],
            'duration_minutes' => $selectedSlot['duration'],
            'status' => $status,
            'player_count' => rand(1, min(4, $court->max_players)),
            'notes' => fake()->optional(0.2)->sentence(),
            'additional_services' => $this->generateAdditionalServices(),
        ];

        // Add status-specific data
        switch ($status) {
            case 'confirmed':
                $reservationData['confirmed_at'] = $date->copy()->subDays(rand(1, 7));
                break;
            case 'cancelled':
                $reservationData['cancelled_at'] = $date->copy()->subDays(rand(0, 3));
                $reservationData['cancellation_reason'] = fake()->randomElement([
                    'Personal emergency',
                    'Weather conditions',
                    'Scheduling conflict',
                    'Illness',
                    'Travel plans changed',
                ]);
                break;
        }

        Reservation::create($reservationData);
    }

    /**
     * Generate available time slots for a court on a specific date
     */
    private function generateAvailableTimeSlots(Court $court, Carbon $date): array
    {
        $slots = [];
        $durations = [30, 45, 60, 90, 120]; // Duration options in minutes

        // Get existing reservations for this court on this date
        $existingReservations = Reservation::where('court_id', $court->id)
            ->where('reservation_date', $date->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->get();

        // Operating hours (6 AM to 10 PM)
        $startHour = 6;
        $endHour = 22;

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 15) {
                foreach ($durations as $duration) {
                    $start = sprintf('%02d:%02d', $hour, $minute);
                    $endTime = Carbon::parse($start)->addMinutes($duration);

                    // Don't go past closing time
                    if ($endTime->hour >= $endHour) {
                        continue;
                    }

                    // Check for conflicts with existing reservations
                    $hasConflict = false;
                    foreach ($existingReservations as $existing) {
                        $existingStart = Carbon::parse($existing->start_time);
                        $existingEnd = Carbon::parse($existing->end_time);
                        $slotStart = Carbon::parse($start);
                        $slotEnd = $endTime;

                        if ($slotStart->lt($existingEnd) && $slotEnd->gt($existingStart)) {
                            $hasConflict = true;
                            break;
                        }
                    }

                    if (!$hasConflict) {
                        $slots[] = [
                            'start' => $start,
                            'end' => $endTime->format('H:i'),
                            'duration' => $duration,
                        ];
                    }
                }
            }
        }

        return $slots;
    }

    /**
     * Get a random user who is a member of the club
     */
    private function getRandomClubMember(Club $club, $allUsers): User
    {
        $clubMembers = $club->users;

        if ($clubMembers->isEmpty()) {
            // If no club members, return a random user
            return $allUsers->random();
        }

        // 70% chance to use a club member, 30% chance to use any user
        if (rand(1, 100) <= 70) {
            return $clubMembers->random();
        } else {
            return $allUsers->random();
        }
    }

    /**
     * Generate additional services
     */
    private function generateAdditionalServices(): array
    {
        $services = [];

        if (rand(1, 100) <= 20) { // 20% chance of equipment rental
            $services['equipment_rental'] = [
                'racquets' => rand(1, 2),
                'cost' => fake()->randomFloat(2, 5, 15),
            ];
        }

        if (rand(1, 100) <= 10) { // 10% chance of coaching
            $services['coaching'] = [
                'type' => fake()->randomElement(['private', 'group']),
                'cost' => fake()->randomFloat(2, 50, 100),
            ];
        }

        if (rand(1, 100) <= 15) { // 15% chance of towel service
            $services['towel_service'] = [
                'count' => rand(1, 4),
                'cost' => fake()->randomFloat(2, 2, 8),
            ];
        }

        return $services;
    }
}
