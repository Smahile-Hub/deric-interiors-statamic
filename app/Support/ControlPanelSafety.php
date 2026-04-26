<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Statamic\Facades\Stache;
use Statamic\Facades\User;
use Statamic\Statamic;

class ControlPanelSafety
{
    public static function actualUserCount(): int
    {
        $directory = base_path('users');

        if (! File::isDirectory($directory)) {
            return 0;
        }

        return collect(File::files($directory))
            ->filter(fn ($file) => $file->getExtension() === 'yaml')
            ->count();
    }

    public static function recoverStaleUserCache(): bool
    {
        if (Statamic::pro()) {
            return true;
        }

        $actualUserCount = static::actualUserCount();
        $cachedUserCount = User::count();

        if ($actualUserCount > 1 || $cachedUserCount <= $actualUserCount) {
            return false;
        }

        Stache::clear();
        Stache::warm();

        return User::count() <= max($actualUserCount, 1);
    }
}
