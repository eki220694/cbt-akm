<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Facades\Filament;

class PanelAccess
{
    public static function user(): ?User
    {
        $user = Filament::auth()->user();

        return $user instanceof User ? $user : null;
    }

    public static function isActive(): bool
    {
        $user = static::user();

        return $user !== null && (bool) $user->is_active;
    }

    public static function isAdmin(): bool
    {
        $user = static::user();

        return $user !== null && (bool) $user->is_active && $user->role === UserRole::Admin;
    }

    public static function isGuru(): bool
    {
        $user = static::user();

        return $user !== null && (bool) $user->is_active && $user->role === UserRole::Guru;
    }
}
