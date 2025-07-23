<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    const STATUSES = ['pending', 'active', 'completed', 'cancelled'];

    protected $fillable = [
        'competition_id',
        'round_id',
        'name',
        'group_number',
        'description',
        'status',
        'max_players',
        'standings',
        'settings',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'group_number' => 'integer',
        'max_players' => 'integer',
        'standings' => 'array',
        'settings' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the competition this group belongs to
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the round this group belongs to
     */
    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    /**
     * Get the participants in this group
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_participants')
            ->withPivot([
                'position',
                'seed',
                'points',
                'wins',
                'losses',
                'draws',
                'statistics',
                'advanced',
                'eliminated',
                'joined_at',
                'notes'
            ])
            ->withTimestamps();
    }

    /**
     * Get active participants (not eliminated)
     */
    public function activeParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivot('eliminated', false);
    }

    /**
     * Get advancing participants
     */
    public function advancingParticipants(): BelongsToMany
    {
        return $this->participants()->wherePivot('advanced', true);
    }

    /**
     * Scope for active groups
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for completed groups
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for groups by competition
     */
    public function scopeForCompetition($query, $competitionId)
    {
        return $query->where('competition_id', $competitionId);
    }

    /**
     * Scope for groups by round
     */
    public function scopeForRound($query, $roundId)
    {
        return $query->where('round_id', $roundId);
    }

    /**
     * Scope to order groups by group number
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('group_number');
    }

    /**
     * Add a participant to this group
     */
    public function addParticipant(User $user, ?int $seed = null): bool
    {
        if ($this->participants()->count() >= $this->max_players) {
            return false;
        }

        if ($this->participants()->where('user_id', $user->id)->exists()) {
            return false; // Already in group
        }

        $this->participants()->attach($user->id, [
            'position' => $this->participants()->count() + 1,
            'seed' => $seed,
            'points' => 0,
            'wins' => 0,
            'losses' => 0,
            'draws' => 0,
            'statistics' => json_encode([
                'matches_played' => 0,
                'total_points' => 0,
                'avg_points_per_match' => 0,
            ]),
            'advanced' => false,
            'eliminated' => false,
            'joined_at' => now(),
        ]);

        $this->updateStandings();
        return true;
    }

    /**
     * Remove a participant from this group
     */
    public function removeParticipant(User $user): bool
    {
        if (!$this->participants()->where('user_id', $user->id)->exists()) {
            return false;
        }

        $this->participants()->detach($user->id);
        $this->updateStandings();
        return true;
    }

    /**
     * Update participant statistics
     */
    public function updateParticipantStatistics(User $user, array $statistics): bool
    {
        if (!$this->participants()->where('user_id', $user->id)->exists()) {
            return false;
        }

        $this->participants()->updateExistingPivot($user->id, [
            'statistics' => json_encode($statistics),
        ]);

        $this->updateStandings();
        return true;
    }

    /**
     * Mark participant as advanced
     */
    public function advanceParticipant(User $user): bool
    {
        if (!$this->participants()->where('user_id', $user->id)->exists()) {
            return false;
        }

        $this->participants()->updateExistingPivot($user->id, [
            'advanced' => true,
        ]);

        return true;
    }

    /**
     * Mark participant as eliminated
     */
    public function eliminateParticipant(User $user): bool
    {
        if (!$this->participants()->where('user_id', $user->id)->exists()) {
            return false;
        }

        $this->participants()->updateExistingPivot($user->id, [
            'eliminated' => true,
        ]);

        return true;
    }

    /**
     * Update group standings based on participant performance
     */
    public function updateStandings(): void
    {
        $participants = $this->participants()->get();
        $standings = [];

        foreach ($participants as $participant) {
            $standings[] = [
                'user_id' => $participant->id,
                'name' => $participant->name,
                'position' => $participant->pivot->position,
                'matches_played' => $participant->pivot->wins + $participant->pivot->losses + $participant->pivot->draws,
                'wins' => $participant->pivot->wins,
                'losses' => $participant->pivot->losses,
                'draws' => $participant->pivot->draws,
                'points' => $participant->pivot->points,
                'win_percentage' => ($participant->pivot->wins + $participant->pivot->losses + $participant->pivot->draws) > 0 ?
                    round($participant->pivot->wins / ($participant->pivot->wins + $participant->pivot->losses + $participant->pivot->draws) * 100, 2) : 0,
            ];
        }

        // Sort standings by points desc, then by wins desc
        usort($standings, function ($a, $b) {
            if ($a['points'] == $b['points']) {
                return $b['wins'] <=> $a['wins'];
            }
            return $b['points'] <=> $a['points'];
        });

        // Update positions
        foreach ($standings as $index => &$standing) {
            $standing['position'] = $index + 1;
        }

        $this->update(['standings' => $standings]);
    }

    /**
     * Get current standings
     */
    public function getStandings(): array
    {
        return $this->standings ?: [];
    }

    /**
     * Get participant by position in standings
     */
    public function getParticipantByPosition(int $position): ?User
    {
        $standings = $this->getStandings();

        foreach ($standings as $standing) {
            if ($standing['position'] === $position) {
                return User::find($standing['user_id']);
            }
        }

        return null;
    }

    /**
     * Check if group can start
     */
    public function canStart(): bool
    {
        return $this->status === 'pending' &&
            $this->participants()->count() >= 2;
    }

    /**
     * Start the group
     */
    public function start(): bool
    {
        if (!$this->canStart()) {
            return false;
        }

        $this->update([
            'status' => 'active',
            'start_date' => $this->start_date ?? now(),
        ]);

        return true;
    }

    /**
     * Complete the group
     */
    public function complete(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $this->update([
            'status' => 'completed',
            'end_date' => $this->end_date ?? now(),
        ]);

        // Determine advancing participants if this is an elimination round
        if ($this->round && $this->round->is_elimination_round) {
            $this->determineAdvancingParticipants();
        }

        return true;
    }

    /**
     * Determine which participants advance to the next round
     */
    private function determineAdvancingParticipants(): void
    {
        if (!$this->round || !$this->round->players_advance) {
            return;
        }

        $standings = $this->getStandings();
        $advanceCount = min($this->round->players_advance, count($standings));

        for ($i = 0; $i < $advanceCount; $i++) {
            if (isset($standings[$i])) {
                $user = User::find($standings[$i]['user_id']);
                if ($user) {
                    $this->advanceParticipant($user);
                }
            }
        }
    }

    /**
     * Check if group is full
     */
    public function isFull(): bool
    {
        return $this->participants()->count() >= $this->max_players;
    }

    /**
     * Get group progress percentage
     */
    public function getProgressAttribute(): int
    {
        if ($this->status === 'completed') {
            return 100;
        }

        if ($this->status === 'pending') {
            return 0;
        }

        // For active groups, we could calculate based on matches played vs expected
        // For now, return 50% for active groups
        return 50;
    }

    /**
     * Get participant count
     */
    public function getParticipantCountAttribute(): int
    {
        return $this->participants()->count();
    }

    /**
     * Get active participant count (not eliminated)
     */
    public function getActiveParticipantCountAttribute(): int
    {
        return $this->activeParticipants()->count();
    }
}
