<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
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
