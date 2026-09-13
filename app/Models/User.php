<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $attributes = [
        'is_active' => true,
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
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // enum check di level aplikasi (portabel sqlite/mysql)
        static::saving(function (self $user): void {
            $role = $user->role instanceof UserRole ? $user->role->value : $user->role;

            if (! in_array($role, ['admin', 'guru'], true)) {
                throw new \InvalidArgumentException("Role [{$role}] tidak valid. Gunakan: admin, guru.");
            }
        });
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_active && $this->role === UserRole::Admin;
    }

    public function isGuru(): bool
    {
        return (bool) $this->is_active && $this->role === UserRole::Guru;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active && in_array($this->role, [UserRole::Admin, UserRole::Guru], true);
    }
}
