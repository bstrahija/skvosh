<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Competition extends Model
{
    use HasFactory;

    const TYPES = ['tournament', 'league', 'single_match', 'ladder', 'round_robin'];
    const FORMATS = ['single_elimination', 'double_elimination', 'round_robin', 'swiss', 'ladder', 'league'];
    const STATUSES = ['draft', 'open', 'in_progress', 'completed', 'cancelled'];
    const PARTICIPANT_ROLES = ['participant', 'admin', 'organizer'];
    const PARTICIPANT_STATUSES = ['registered', 'confirmed', 'withdrawn', 'eliminated', 'disqualified'];

    protected $fillable = [
        'club_id',
        'created_by',
        'name',
        'slug',
        'description',
        'type',
        'format',
        'sport',
        'status',
        'is_ranked',
        'is_public',
        'max_participants',
        'min_participants',
        'entry_fee',
        'prize_structure',
        'registration_start',
        'registration_end',
        'start_date',
        'end_date',
        'rules',
        'settings',
        'requirements',
        'image_path',
        'auto_schedule',
        'rounds_completed',
        'total_rounds',
    ];

    protected $casts = [
        'is_ranked' => 'boolean',
        'is_public' => 'boolean',
        'max_participants' => 'integer',
        'min_participants' => 'integer',
        'entry_fee' => 'decimal:2',
        'prize_structure' => 'array',
        'registration_start' => 'date',
        'registration_end' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'rules' => 'array',
        'settings' => 'array',
        'auto_schedule' => 'boolean',
        'rounds_completed' => 'integer',
        'total_rounds' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($competition) {
            if (empty($competition->slug)) {
                $competition->slug = Str::slug($competition->name);
            }
        });

        static::updating(function ($competition) {
            if ($competition->isDirty('name') && empty($competition->slug)) {
                $competition->slug = Str::slug($competition->name);
            }
        });
    }

    /**
     * Get the club that hosts this competition
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Get the user who created this competition
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all participants in this competition
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'competition_participants')
            ->withPivot([
                'role',
                'status',
                'seed',
                'current_ranking',
                'points',
                'wins',
                'losses',
                'draws',
                'statistics',
                'entry_fee_paid',
                'fee_paid',
                'registered_at',
                'confirmed_at',
                'withdrawn_at',
                'withdrawal_reason',
                'notes'
            ])
            ->withTimestamps();
    }

    /**
     * Get only active participants (not withdrawn or disqualified)
     */
    public function activeParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivotIn('status', ['registered', 'confirmed', 'eliminated']);
    }

    /**
     * Get competition admins
     */
    public function admins(): BelongsToMany
    {
        return $this->participants()->wherePivot('role', 'admin');
    }

    /**
     * Get competition organizers
     */
    public function organizers(): BelongsToMany
    {
        return $this->participants()->wherePivot('role', 'organizer');
    }

    /**
     * Get only players (not admins/organizers)
     */
    public function players(): BelongsToMany
    {
        return $this->participants()->wherePivot('role', 'participant');
    }

    /**
     * Get confirmed participants
     */
    public function confirmedParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivot('status', 'confirmed');
    }

    /**
     * Get all rounds in this competition
     */
    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class)->orderBy('round_number');
    }

    /**
     * Get all groups across all rounds in this competition
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Get the current active round
     */
    public function currentRound()
    {
        return $this->rounds()->where('status', 'active')->first();
    }

    /**
     * Get the next pending round
     */
    public function nextRound()
    {
        return $this->rounds()->where('status', 'pending')->orderBy('round_number')->first();
    }

    /**
     * Scope for active competitions
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    /**
     * Scope for public competitions
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for ranked competitions
     */
    public function scopeRanked($query)
    {
        return $query->where('is_ranked', true);
    }

    /**
     * Scope for competitions by sport
     */
    public function scopeBySport($query, string $sport)
    {
        return $query->where('sport', $sport);
    }

    /**
     * Scope for competitions by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for upcoming competitions
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())
            ->whereIn('status', ['draft', 'open']);
    }

    /**
     * Scope for ongoing competitions
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Check if registration is open
     */
    public function isRegistrationOpen(): bool
    {
        if ($this->status !== 'open') {
            return false;
        }

        $now = now();

        if ($this->registration_start && $now->lt($this->registration_start)) {
            return false;
        }

        if ($this->registration_end && $now->gt($this->registration_end)) {
            return false;
        }

        return true;
    }

    /**
     * Check if competition is full
     */
    public function isFull(): bool
    {
        if (!$this->max_participants) {
            return false;
        }

        return $this->confirmedParticipants()->count() >= $this->max_participants;
    }

    /**
     * Check if user can register
     */
    public function canUserRegister(User $user): bool
    {
        if (!$this->isRegistrationOpen() || $this->isFull()) {
            return false;
        }

        // Check if user is already registered
        if ($this->participants()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Register a user for the competition
     */
    public function registerUser(User $user, array $data = []): bool
    {
        if (!$this->canUserRegister($user)) {
            return false;
        }

        $pivotData = array_merge([
            'role' => 'participant',
            'status' => 'registered',
            'registered_at' => now(),
            'entry_fee_paid' => $this->entry_fee,
            'fee_paid' => $this->entry_fee ? false : true, // Auto-mark as paid if no fee
        ], $data);

        $this->participants()->attach($user->id, $pivotData);
        return true;
    }

    /**
     * Add an admin to the competition
     */
    public function addAdmin(User $user): bool
    {
        if ($this->participants()->where('user_id', $user->id)->exists()) {
            // Update existing participation to admin
            $this->participants()->updateExistingPivot($user->id, [
                'role' => 'admin',
                'status' => 'confirmed',
            ]);
        } else {
            // Add as new admin
            $this->participants()->attach($user->id, [
                'role' => 'admin',
                'status' => 'confirmed',
                'registered_at' => now(),
                'confirmed_at' => now(),
                'fee_paid' => true,
            ]);
        }

        return true;
    }

    /**
     * Get current participant count
     */
    public function getParticipantCountAttribute(): int
    {
        return $this->confirmedParticipants()->count();
    }

    /**
     * Get registration status message
     */
    public function getRegistrationStatusAttribute(): string
    {
        if (!$this->isRegistrationOpen()) {
            if ($this->status === 'draft') {
                return 'Registration not yet open';
            } elseif ($this->status === 'in_progress') {
                return 'Competition in progress';
            } elseif ($this->status === 'completed') {
                return 'Competition completed';
            } else {
                return 'Registration closed';
            }
        }

        if ($this->isFull()) {
            return 'Competition full';
        }

        return 'Registration open';
    }

    /**
     * Get formatted entry fee
     */
    public function getFormattedEntryFeeAttribute(): string
    {
        return $this->entry_fee ? '$' . number_format($this->entry_fee, 2) : 'Free';
    }

    /**
     * Get route key name for URL generation
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Start the competition
     */
    public function start(): bool
    {
        if ($this->status !== 'open') {
            return false;
        }

        $confirmedCount = $this->confirmedParticipants()->count();
        if ($confirmedCount < $this->min_participants) {
            return false;
        }

        $this->update([
            'status' => 'in_progress',
            'start_date' => $this->start_date ?? now(),
        ]);

        return true;
    }

    /**
     * Complete the competition
     */
    public function complete(): bool
    {
        if ($this->status !== 'in_progress') {
            return false;
        }

        $this->update([
            'status' => 'completed',
            'end_date' => $this->end_date ?? now(),
        ]);

        return true;
    }

    /**
     * Cancel the competition
     */
    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'rules' => array_merge($this->rules ?? [], ['cancellation_reason' => $reason]),
        ]);

        return true;
    }

    /**
     * Get leaderboard for the competition
     */
    public function getLeaderboard(): array
    {
        return $this->participants()
            ->wherePivot('role', 'participant')
            ->orderByPivot('current_ranking', 'asc')
            ->orderByPivot('points', 'desc')
            ->orderByPivot('wins', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'user' => $user,
                    'ranking' => $user->pivot->current_ranking,
                    'points' => $user->pivot->points,
                    'wins' => $user->pivot->wins,
                    'losses' => $user->pivot->losses,
                    'draws' => $user->pivot->draws,
                    'statistics' => $user->pivot->statistics,
                ];
            })
            ->toArray();
    }
}
