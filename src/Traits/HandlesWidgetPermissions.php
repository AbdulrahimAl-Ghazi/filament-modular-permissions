<?php

namespace Abdulrahim\FilamentModularPermissions\Traits;

use Illuminate\Support\Str;

trait HandlesWidgetPermissions
{
    /**
     * Determine if the widget should be visible based on permissions.
     * Guards against unauthenticated access to prevent a TypeError.
     */
    public static function canView(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $widgetName = Str::snake(class_basename(static::class));

        return auth()->user()->can("view_{$widgetName}");
    }
}
