<?php

namespace Abdulrahim\FilamentModularPermissions\Traits;

use Illuminate\Support\Str;

trait HandlesWidgetPermissions
{
    /**
     * Determine if the widget should be visible based on permissions.
     */
    public static function canView(): bool
    {
        $widgetName = Str::snake(class_basename(static::class));
        
        return auth()->user()->can("view_{$widgetName}");
    }
}
