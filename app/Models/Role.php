<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->withPivot('assigned_at');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function givePermission(Permission|string $permission): void
    {
        $perm = is_string($permission)
            ? Permission::firstOrCreate(['name' => $permission], ['label' => $permission, 'module' => 'system'])
            : $permission;

        $this->permissions()->syncWithoutDetaching([$perm->id]);
    }

    public function revokePermission(Permission|string $permission): void
    {
        $perm = is_string($permission)
            ? Permission::where('name', $permission)->first()
            : $permission;

        if ($perm) {
            $this->permissions()->detach($perm->id);
        }
    }
}
