<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Round extends Model
{
    use HasFactory;

    const STATUSES = ['pending', 'active', 'completed', 'cancelled'];

    protected $fillable = [
        'competition_id',
        'name',
        'round_number',
        'description',
        'status',
        'start_date',
        'end_date',
        'total_groups',
        'settings',
        'is_elimination_round',
        'players_advance',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_groups' => 'integer',
        'settings' => 'array',
        'is_elimination_round' => 'boolean',
        'players_advance' => 'integer',
        'round_number' => 'integer',
    ];

    /**
     * Get the competition this round belongs to
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get all groups in this round
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Get active groups in this round
     */
    public function activeGroups(): HasMany
    {
        return $this->groups()->where('status', 'active');
    }

    /**
     * Get completed groups in this round
     */
    public function completedGroups(): HasMany
    {
        return $this->groups()->where('status', 'completed');
    }

    /**
     * Scope for active rounds
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for completed rounds
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for rounds by competition
     */
    public function scopeForCompetition($query, $competitionId)
    {
        return $query->where('competition_id', $competitionId);
    }

    /**
     * Scope to order rounds by round number
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('round_number');
    }

    /**
     * Check if round is ready to start
     */
    public function canStart(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        // Check if all groups have participants
        $groupsWithParticipants = $this->groups()->whereHas('participants')->count();
        return $groupsWithParticipants === $this->total_groups;
    }

    /**
     * Start the round
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

        // Start all groups in this round
        $this->groups()->update(['status' => 'active']);

        return true;
    }

    /**
     * Complete the round
     */
    public function complete(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        // Check if all groups are completed
        $completedGroups = $this->groups()->where('status', 'completed')->count();
        if ($completedGroups !== $this->total_groups) {
            return false;
        }

        $this->update([
            'status' => 'completed',
            'end_date' => $this->end_date ?? now(),
        ]);

        return true;
    }

    /**
     * Get all participants across all groups in this round
     */
    public function getAllParticipants()
    {
        return User::whereHas('groupParticipations', function ($query) {
            $query->whereIn('group_id', $this->groups()->pluck('id'));
        })->get();
    }

    /**
     * Get advancing participants from this round
     */
    public function getAdvancingParticipants()
    {
        if (!$this->is_elimination_round || !$this->players_advance) {
            return collect();
        }

        return User::whereHas('groupParticipations', function ($query) {
            $query->whereIn('group_id', $this->groups()->pluck('id'))
                ->where('advanced', true);
        })->get();
    }

    /**
     * Generate groups for this round
     */
    public function generateGroups(array $participants = []): bool
    {
        if ($this->groups()->count() > 0) {
            return false; // Groups already exist
        }

        $participantCount = count($participants);
        $groupSize = max(2, intval($participantCount / $this->total_groups));

        for ($i = 1; $i <= $this->total_groups; $i++) {
            $group = Group::create([
                'competition_id' => $this->competition_id,
                'round_id' => $this->id,
                'name' => $this->generateGroupName($i),
                'group_number' => $i,
                'max_players' => $groupSize + 1, // Allow some flexibility
                'status' => 'pending',
            ]);

            // Assign participants to this group
            $groupParticipants = array_slice($participants, ($i - 1) * $groupSize, $groupSize);
            foreach ($groupParticipants as $index => $participant) {
                $group->addParticipant($participant, $index + 1);
            }
        }

        return true;
    }

    /**
     * Generate group name based on round and group number
     */
    private function generateGroupName(int $groupNumber): string
    {
        $letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];

        if ($this->total_groups <= 12) {
            return 'Group ' . $letters[$groupNumber - 1];
        } else {
            return 'Group ' . $groupNumber;
        }
    }

    /**
     * Get round progress percentage
     */
    public function getProgressAttribute(): int
    {
        if ($this->status === 'completed') {
            return 100;
        }

        if ($this->status === 'pending') {
            return 0;
        }

        $totalGroups = $this->total_groups;
        $completedGroups = $this->groups()->where('status', 'completed')->count();

        if ($totalGroups === 0) {
            return 0;
        }

        return intval(($completedGroups / $totalGroups) * 100);
    }

    /**
     * Check if this is the final round
     */
    public function isFinalRound(): bool
    {
        return $this->round_number === $this->competition->total_rounds;
    }

    /**
     * Get the next round
     */
    public function nextRound()
    {
        return $this->competition->rounds()
            ->where('round_number', '>', $this->round_number)
            ->orderBy('round_number')
            ->first();
    }

    /**
     * Get the previous round
     */
    public function previousRound()
    {
        return $this->competition->rounds()
            ->where('round_number', '<', $this->round_number)
            ->orderBy('round_number', 'desc')
            ->first();
    }
}
