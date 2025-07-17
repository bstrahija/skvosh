<?php

namespace Database\Factories;

use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $court = Court::factory();
        $user = User::factory();

        // Generate a future date (next 30 days)
        $reservationDate = $this->faker->dateTimeBetween('now', '+30 days');

        // Generate time slots in 15-minute increments
        $timeSlots = $this->generateTimeSlots();
        $selectedSlot = $this->faker->randomElement($timeSlots);

        return [
            'court_id' => $court,
            'user_id' => $user,
            'reservation_date' => $reservationDate->format('Y-m-d'),
            'start_time' => $selectedSlot['start'],
            'end_time' => $selectedSlot['end'],
            'duration_minutes' => $selectedSlot['duration'],
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            'total_cost' => null, // Will be calculated by model
            'player_count' => $this->faker->numberBetween(1, 4),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'additional_services' => $this->generateAdditionalServices(),
            'confirmed_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }

    /**
     * Generate realistic time slots in 15-minute increments
     */
    private function generateTimeSlots(): array
    {
        $slots = [];
        $durations = [30, 45, 60, 90, 120]; // Duration options in minutes

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

                    $slots[] = [
                        'start' => $start,
                        'end' => $endTime->format('H:i'),
                        'duration' => $duration,
                    ];
                }
            }
        }

        return $slots;
    }

    /**
     * Generate additional services
     */
    private function generateAdditionalServices(): array
    {
        $services = [];

        if ($this->faker->boolean(20)) { // 20% chance of equipment rental
            $services['equipment_rental'] = [
                'racquets' => $this->faker->numberBetween(1, 2),
                'cost' => $this->faker->randomFloat(2, 5, 15),
            ];
        }

        if ($this->faker->boolean(10)) { // 10% chance of coaching
            $services['coaching'] = [
                'type' => $this->faker->randomElement(['private', 'group']),
                'cost' => $this->faker->randomFloat(2, 50, 100),
            ];
        }

        if ($this->faker->boolean(15)) { // 15% chance of towel service
            $services['towel_service'] = [
                'count' => $this->faker->numberBetween(1, 4),
                'cost' => $this->faker->randomFloat(2, 2, 8),
            ];
        }

        return $services;
    }

    /**
     * State for confirmed reservations
     */
    public function confirmed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'confirmed',
                'confirmed_at' => now()->subDays(rand(0, 7)),
            ];
        });
    }

    /**
     * State for cancelled reservations
     */
    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
                'cancelled_at' => now()->subDays(rand(0, 3)),
                'cancellation_reason' => $this->faker->randomElement([
                    'Personal emergency',
                    'Weather conditions',
                    'Scheduling conflict',
                    'Illness',
                    'Travel plans changed',
                ]),
            ];
        });
    }

    /**
     * State for completed reservations
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'reservation_date' => $this->faker->dateTimeBetween('-30 days', '-1 day')->format('Y-m-d'),
                'confirmed_at' => now()->subDays(rand(7, 30)),
            ];
        });
    }

    /**
     * State for future reservations
     */
    public function future(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'reservation_date' => $this->faker->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
                'status' => $this->faker->randomElement(['pending', 'confirmed']),
            ];
        });
    }

    /**
     * State for today's reservations
     */
    public function today(): static
    {
        return $this->state(function (array $attributes) {
            $timeSlots = $this->generateTimeSlots();

            // Filter to future time slots today
            $futureSlots = array_filter($timeSlots, function ($slot) {
                return Carbon::parse($slot['start'])->isAfter(now());
            });

            if (empty($futureSlots)) {
                // If no future slots today, use a random future slot
                $selectedSlot = $this->faker->randomElement($timeSlots);
            } else {
                $selectedSlot = $this->faker->randomElement($futureSlots);
            }

            return [
                'reservation_date' => now()->format('Y-m-d'),
                'start_time' => $selectedSlot['start'],
                'end_time' => $selectedSlot['end'],
                'duration_minutes' => $selectedSlot['duration'],
                'status' => $this->faker->randomElement(['confirmed', 'pending']),
            ];
        });
    }

    /**
     * State for specific duration
     */
    public function duration(int $minutes): static
    {
        return $this->state(function (array $attributes) use ($minutes) {
            // Ensure duration is in 15-minute increments and at least 15 minutes
            $validDuration = max(15, ceil($minutes / 15) * 15);

            $startHour = rand(6, 20); // 6 AM to 8 PM start times
            $startMinute = [0, 15, 30, 45][rand(0, 3)];
            $start = sprintf('%02d:%02d', $startHour, $startMinute);
            $end = Carbon::parse($start)->addMinutes($validDuration)->format('H:i');

            return [
                'start_time' => $start,
                'end_time' => $end,
                'duration_minutes' => $validDuration,
            ];
        });
    }
}
