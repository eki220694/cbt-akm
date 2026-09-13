<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Support\PanelAccess;
use Illuminate\Database\Eloquent\Model;

trait RestrictsByRole
{
    // hak guru per resource: 'full' (CRUD penuh), 'view' (lihat saja), 'none' (tutup).
    // definisikan `protected static string $guruAccess = '...';` di class pemakai.
    protected static function guruAccessLevel(): string
    {
        return isset(static::$guruAccess) ? (string) static::$guruAccess : 'none';
    }

    public static function canAccess(): bool
    {
        if (PanelAccess::isAdmin()) {
            return true;
        }

        return PanelAccess::isGuru() && static::guruAccessLevel() !== 'none';
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        if (PanelAccess::isAdmin()) {
            return true;
        }

        return PanelAccess::isGuru() && static::guruAccessLevel() === 'full';
    }

    public static function canEdit(Model $record): bool
    {
        return static::canCreate();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canCreate();
    }

    public static function canDeleteAny(): bool
    {
        return static::canCreate();
    }
}
