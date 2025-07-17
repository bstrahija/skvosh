<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'email',
        'phone',
        'website',
        'address_line_1',
        'address_line_2',
        'city',
        'state_province',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'is_active',
        'operating_hours',
        'amenities',
        'sports',
        'logo_path',
        'gallery_images',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'operating_hours' => 'array',
        'amenities' => 'array',
        'sports' => 'array',
        'gallery_images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($club) {
            if (empty($club->slug)) {
                $club->slug = Str::slug($club->name);
            }
        });

        static::updating(function ($club) {
            if ($club->isDirty('name') && empty($club->slug)) {
                $club->slug = Str::slug($club->name);
            }
        });
    }

    /**
     * Get all competitions hosted by this club
     */
    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }

    /**
     * Get active competitions for this club
     */
    public function activeCompetitions(): HasMany
    {
        return $this->competitions()->active();
    }

    /**
     * Get upcoming competitions for this club
     */
    public function upcomingCompetitions(): HasMany
    {
        return $this->competitions()->upcoming();
    }

    /**
     * Get all courts belonging to this club
     */
    public function courts(): HasMany
    {
        return $this->hasMany(Court::class);
    }

    /**
     * Get only active courts
     */
    public function activeCourts(): HasMany
    {
        return $this->courts()->where('is_active', true);
    }

    /**
     * Get courts by type
     */
    public function courtsByType(string $type): HasMany
    {
        return $this->courts()->where('type', $type);
    }

    /**
     * Get all users associated with this club
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'joined_at', 'expires_at', 'is_active', 'permissions'])
            ->withTimestamps();
    }

    /**
     * Get only members of this club
     */
    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'member');
    }

    /**
     * Get only admins of this club
     */
    public function admins(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'admin');
    }

    /**
     * Get only owners of this club
     */
    public function owners(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'owner');
    }

    /**
     * Get active memberships only
     */
    public function activeUsers(): BelongsToMany
    {
        return $this->users()->wherePivot('is_active', true);
    }

    /**
     * Get the full address as a string
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state_province,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Scope for active clubs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for clubs by location
     */
    public function scopeNearby($query, $latitude, $longitude, $radiusInKm = 50)
    {
        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("
                *,
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
            ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radiusInKm)
            ->orderBy('distance');
    }

    /**
     * Check if club supports a specific sport
     */
    public function supportsSport(string $sport): bool
    {
        return in_array($sport, $this->sports ?? []);
    }

    /**
     * Get URL route for the club
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
