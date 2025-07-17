<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    const MINIMUM_DURATION_MINUTES = 15;
    const VALID_STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];

    protected $fillable = [
        'court_id',
        'user_id',
        'reservation_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'status',
        'total_cost',
        'player_count',
        'notes',
        'additional_services',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration_minutes' => 'integer',
        'total_cost' => 'decimal:2',
        'player_count' => 'integer',
        'additional_services' => 'array',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            $reservation->calculateDuration();
            $reservation->calculateTotalCost();
        });

        static::updating(function ($reservation) {
            if ($reservation->isDirty(['start_time', 'end_time'])) {
                $reservation->calculateDuration();
                $reservation->calculateTotalCost();
            }
        });
    }

    /**
     * Get the court this reservation belongs to
     */
    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    /**
     * Get the user who made this reservation
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate duration in minutes between start and end time
     */
    public function calculateDuration(): void
    {
        if ($this->start_time && $this->end_time) {
            $start = Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);
            $this->duration_minutes = $start->diffInMinutes($end);
        }
    }

    /**
     * Calculate total cost based on court hourly rate and duration
     */
    public function calculateTotalCost(): void
    {
        if ($this->court && $this->duration_minutes && $this->court->hourly_rate) {
            $hours = $this->duration_minutes / 60;
            $this->total_cost = $this->court->hourly_rate * $hours;
        }
    }

    /**
     * Scope for reservations on a specific date
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('reservation_date', $date);
    }

    /**
     * Scope for reservations by status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for confirmed reservations
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for active reservations (not cancelled)
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled']);
    }

    /**
     * Scope for reservations in the future
     */
    public function scopeFuture($query)
    {
        return $query->where(function ($q) {
            $q->where('reservation_date', '>', now()->toDateString())
                ->orWhere(function ($q2) {
                    $q2->where('reservation_date', '=', now()->toDateString())
                        ->where('start_time', '>', now()->toTimeString());
                });
        });
    }

    /**
     * Scope for reservations for a specific court
     */
    public function scopeForCourt($query, $courtId)
    {
        return $query->where('court_id', $courtId);
    }

    /**
     * Check if reservation conflicts with another time slot
     */
    public function conflictsWith($date, $startTime, $endTime, $excludeId = null): bool
    {
        $query = static::where('court_id', $this->court_id)
            ->where('reservation_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($q2) use ($startTime, $endTime) {
                        $q2->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Confirm the reservation
     */
    public function confirm(): bool
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        return true;
    }

    /**
     * Cancel the reservation
     */
    public function cancel(string $reason = null): bool
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Mark reservation as completed
     */
    public function complete(): bool
    {
        $this->update(['status' => 'completed']);
        return true;
    }

    /**
     * Check if reservation can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) && $this->isFuture();
    }

    /**
     * Check if reservation is in the future
     */
    public function isFuture(): bool
    {
        $reservationDateTime = Carbon::parse($this->reservation_date->format('Y-m-d') . ' ' . $this->start_time);
        return $reservationDateTime->isAfter(now());
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        return Carbon::parse($this->start_time)->format('H:i') . ' - ' . Carbon::parse($this->end_time)->format('H:i');
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = intval($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return $hours . 'h ' . $minutes . 'm';
        } elseif ($hours > 0) {
            return $hours . 'h';
        } else {
            return $minutes . 'm';
        }
    }

    /**
     * Get formatted cost
     */
    public function getFormattedCostAttribute(): string
    {
        return $this->total_cost ? '$' . number_format($this->total_cost, 2) : 'TBD';
    }

    /**
     * Validate reservation time slot
     */
    public function validateTimeSlot(): array
    {
        $errors = [];

        // Check minimum duration
        if ($this->duration_minutes < self::MINIMUM_DURATION_MINUTES) {
            $errors[] = 'Minimum reservation duration is ' . self::MINIMUM_DURATION_MINUTES . ' minutes';
        }

        // Check if duration is in 15-minute increments
        if ($this->duration_minutes % 15 !== 0) {
            $errors[] = 'Reservation duration must be in 15-minute increments';
        }

        // Check for conflicts
        if ($this->conflictsWith($this->reservation_date, $this->start_time, $this->end_time, $this->id)) {
            $errors[] = 'This time slot conflicts with an existing reservation';
        }

        // Check if reservation is in the past
        if (!$this->isFuture()) {
            $errors[] = 'Cannot make reservations in the past';
        }

        return $errors;
    }
}
