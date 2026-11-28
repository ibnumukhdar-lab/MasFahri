<?php

namespace App\Filament\Concerns;

/**
 * Menu Pengguna (penetapan peran) HANYA untuk super admin.
 * Operator website sengaja tidak boleh mengubah peran siapa pun.
 */
trait HanyaSuperAdmin
{
    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->superAdmin();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return (bool) auth()->user()?->superAdmin();
    }

    public static function canDelete($record): bool
    {
        return (bool) auth()->user()?->superAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return (bool) auth()->user()?->superAdmin();
    }
}
