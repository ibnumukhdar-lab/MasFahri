<?php

namespace App\Filament\Concerns;

/**
 * Menu yang HANYA untuk pengelola penuh (super admin & operator website).
 * Akun dengan peran "panitia_spmb" tidak melihat menu ini dan tidak bisa
 * membukanya lewat URL langsung.
 */
trait HanyaPengelolaPenuh
{
    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->bolehSemua();
    }

    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->bolehSemua();
    }

    public static function canEdit($record): bool
    {
        return (bool) auth()->user()?->bolehSemua();
    }

    public static function canDelete($record): bool
    {
        return (bool) auth()->user()?->bolehSemua();
    }

    public static function canDeleteAny(): bool
    {
        return (bool) auth()->user()?->bolehSemua();
    }
}
