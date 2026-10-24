<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;

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
        'username',
        'email',
        'phone',
        'preferred_locale',
        'password',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Roles relationship
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('assigned_at');
    }

    /**
     * Assigned locations relationship
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'user_locations')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    /**
     * Check if user has specific role or any of the given roles
     */
    public function hasRole(string|array $roles): bool
    {
        $roleList = is_array($roles) ? $roles : [$roles];
        
        return $this->roles->contains(function (Role $role) use ($roleList) {
            return in_array($role->name, $roleList, true);
        });
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->allPermissions()->contains('name', $permission);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $all = $this->allPermissions()->pluck('name')->toArray();
        foreach ($permissions as $p) {
            if (in_array($p, $all, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve all unique permissions across user's roles
     */
    public function allPermissions(): Collection
    {
        return $this->roles->flatMap(function (Role $role) {
            return $role->permissions;
        })->unique('id');
    }

    /**
     * Check if user is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is Owner (Read-Only)
     */
    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    /**
     * Check if user is allowed to access given location
     */
    public function canAccessLocation(int|string $locationId): bool
    {
        if ($this->isSuperAdmin() || $this->isOwner()) {
            return true;
        }

        return $this->locations->contains('id', (int) $locationId);
    }

    /**
     * Get active operational location from session or fallback to default
     */
    public function getActiveLocation(): ?Location
    {
        $sessionLocId = Session::get('active_location_id');

        if ($sessionLocId) {
            $location = Location::active()->find($sessionLocId);
            if ($location && $this->canAccessLocation($location->id)) {
                return $location;
            }
        }

        // Fallback to default user location
        $defaultLoc = $this->locations()->wherePivot('is_default', true)->first();
        if ($defaultLoc) {
            Session::put('active_location_id', $defaultLoc->id);
            return $defaultLoc;
        }

        // Fallback to first assigned location
        $firstLoc = $this->locations()->first();
        if ($firstLoc) {
            Session::put('active_location_id', $firstLoc->id);
            return $firstLoc;
        }

        // Fallback for global users (Owner / Super Admin)
        if ($this->isSuperAdmin() || $this->isOwner()) {
            $firstActive = Location::active()->where('type', '!=', Location::TYPE_TRANSIT)->first();
            if ($firstActive) {
                Session::put('active_location_id', $firstActive->id);
                return $firstActive;
            }
        }

        return null;
    }

    /**
     * Primary role label for UI badge
     */
    public function primaryRole(): ?Role
    {
        return $this->roles->first();
    }
}
