<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password', 'role', 'active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /** Admin ou Super Admin */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin'], true);
    }

    public function isMaintenance(): bool
    {
        return $this->role === 'maintenance';
    }

    public function roleLabel(): string
    {
        return config('intendance.roles.'.$this->role, $this->role);
    }

    public function bureaux(): BelongsToMany
    {
        return $this->belongsToMany(Bureau::class, 'bureau_user')->withTimestamps();
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function incidentsAssignes(): HasMany
    {
        return $this->hasMany(Incident::class, 'assigne_a');
    }
}
