<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Zap\Models\Concerns\HasSchedules;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasSchedules, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get competitions this user has created
     */
    public function createdCompetitions(): HasMany
    {
        return $this->hasMany(Competition::class, 'created_by');
    }

    /**
     * Get competitions this user participates in
     */
    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class, 'competition_participants')
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
     * Get competitions where user is an admin
     */
    public function adminCompetitions(): BelongsToMany
    {
        return $this->competitions()->wherePivot('role', 'admin');
    }

    /**
     * Get competitions where user is a participant
     */
    public function participantCompetitions(): BelongsToMany
    {
        return $this->competitions()->wherePivot('role', 'participant');
    }

    /**
     * Get active competitions for this user
     */
    public function activeCompetitions(): BelongsToMany
    {
        return $this->competitions()->wherePivotIn('status', ['registered', 'confirmed']);
    }

    /**
     * Get all reservations for this user
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get active reservations for this user
     */
    public function activeReservations(): HasMany
    {
        return $this->reservations()->active();
    }

    /**
     * Get future reservations for this user
     */
    public function futureReservations(): HasMany
    {
        return $this->reservations()->future();
    }

    /**
     * Get all clubs this user belongs to
     */
    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)
            ->withPivot(['role', 'joined_at', 'expires_at', 'is_active', 'permissions'])
            ->withTimestamps();
    }

    /**
     * Get only active club memberships
     */
    public function activeClubs(): BelongsToMany
    {
        return $this->clubs()->wherePivot('is_active', true);
    }

    /**
     * Get clubs where user is an owner
     */
    public function ownedClubs(): BelongsToMany
    {
        return $this->clubs()->wherePivot('role', 'owner');
    }

    /**
     * Get clubs where user is an admin
     */
    public function adminClubs(): BelongsToMany
    {
        return $this->clubs()->wherePivot('role', 'admin');
    }

    /**
     * Get clubs where user is a member
     */
    public function memberClubs(): BelongsToMany
    {
        return $this->clubs()->wherePivot('role', 'member');
    }

    /**
     * Get all group participations for this user
     */
    public function groupParticipations(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_participants')
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
     * Get active group participations (not eliminated)
     */
    public function activeGroupParticipations(): BelongsToMany
    {
        return $this->groupParticipations()->wherePivot('eliminated', false);
    }

    /**
     * Get groups where user has advanced
     */
    public function advancedGroups(): BelongsToMany
    {
        return $this->groupParticipations()->wherePivot('advanced', true);
    }

    /**
     * Check if user has a specific role at a club
     */
    public function hasRoleAtClub(Club $club, string $role): bool
    {
        return $this->clubs()
            ->where('clubs.id', $club->id)
            ->wherePivot('role', $role)
            ->wherePivot('is_active', true)
            ->exists();
    }

    /**
     * Check if user is an owner of a club
     */
    public function isOwnerOf(Club $club): bool
    {
        return $this->hasRoleAtClub($club, 'owner');
    }

    /**
     * Check if user is an admin of a club
     */
    public function isAdminOf(Club $club): bool
    {
        return $this->hasRoleAtClub($club, 'admin');
    }

    /**
     * Check if user is a member of a club
     */
    public function isMemberOf(Club $club): bool
    {
        return $this->hasRoleAtClub($club, 'member');
    }

    /**
     * Check if user can manage a club (owner or admin)
     */
    public function canManage(Club $club): bool
    {
        return $this->isOwnerOf($club) || $this->isAdminOf($club);
    }

    /**
     * Get user's permissions for a specific club
     */
    public function getClubPermissions(Club $club): array
    {
        $membership = $this->clubs()
            ->where('clubs.id', $club->id)
            ->wherePivot('is_active', true)
            ->first();

        if (!$membership) {
            return [];
        }

        $permissions = json_decode($membership->pivot->permissions ?? '[]', true);
        return is_array($permissions) ? $permissions : [];
    }

    /**
     * Check if user has a specific permission at a club
     */
    public function hasPermissionAt(Club $club, string $permission): bool
    {
        return in_array($permission, $this->getClubPermissions($club));
    }
}
