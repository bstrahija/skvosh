<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'name',
        'type',
        'description',
        'is_active',
        'hourly_rate',
        'amenities',
        'available_hours',
        'max_players',
        'surface_type',
        'equipment_included',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'amenities' => 'array',
        'available_hours' => 'array',
        'max_players' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the club that owns this court
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Scope for active courts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for courts by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for courts by club
     */
    public function scopeForClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }

    /**
     * Scope to order courts by their sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Check if court is available for booking
     */
    public function isAvailableForBooking(): bool
    {
        return $this->is_active && $this->club->is_active;
    }

    /**
     * Get formatted hourly rate
     */
    public function getFormattedRateAttribute(): string
    {
        if (!$this->hourly_rate) {
            return 'Contact club for pricing';
        }

        return '$' . number_format($this->hourly_rate, 2) . '/hour';
    }

    /**
     * Get full court name with club
     */
    public function getFullNameAttribute(): string
    {
        return $this->club->name . ' - ' . $this->name;
    }

    /**
     * Check if court has a specific amenity
     */
    public function hasAmenity(string $amenity): bool
    {
        return in_array($amenity, $this->amenities ?? []);
    }

    /**
     * Get available time slots for a specific day
     */
    public function getAvailableTimeSlotsForDay(string $day): array
    {
        $availableHours = $this->available_hours ?? [];
        
        // If no specific hours set for court, use club hours
        if (empty($availableHours)) {
            $clubHours = $this->club->operating_hours ?? [];
            return $clubHours[$day] ?? [];
        }

        return $availableHours[$day] ?? [];
    }
}
