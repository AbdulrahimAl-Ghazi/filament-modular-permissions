<?php

namespace Abdulrahim\FilamentModularPermissions;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Filament\Facades\Filament;

class FilamentModularPermissionsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-modular-permissions';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        // 1. Same Gate Logic (Transferred from ServiceProvider)
        Gate::before(function ($user, $ability, $args) {
            $superAdminRole = config('filament-modular-permissions.super_admin_role_name', 'super_admin');
            
            try {
                if ($user->hasRole($superAdminRole)) {
                    return true;
                }
            } catch (\Throwable $e) {
                return null;
            }

            if (config('filament-modular-permissions.auto_hide_resources', true)) {
                $abilityMap = [
                    'viewAny' => 'view_any', 'view' => 'view', 'create' => 'create',
                    'update' => 'update', 'delete' => 'delete', 'deleteAny' => 'delete_any',
                    'forceDelete' => 'force_delete', 'forceDeleteAny' => 'force_delete_any',
                    'restore' => 'restore', 'restoreAny' => 'restore_any',
                    'reorder' => 'reorder', 'replicate' => 'replicate',
                ];

                if (isset($abilityMap[$ability]) && isset($args[0])) {
                    $subject    = $args[0];
                    $modelClass = is_object($subject) ? get_class($subject) : $subject;

                    if (is_string($modelClass) && class_exists($modelClass) && is_subclass_of($modelClass, \Illuminate\Database\Eloquent\Model::class)) {
                        $modelName  = Str::snake(class_basename($modelClass));
                        $permission = "{$abilityMap[$ability]}_{$modelName}";

                        try {
                            return $user->hasPermissionTo($permission) ? true : false;
                        } catch (\Throwable $e) {
                            return false;
                        }
                    }
                }
            }

            return null;
        });

        // 2. Same Widget Logic (Transferred from ServiceProvider)
        if (config('filament-modular-permissions.auto_hide_resources', true)) {
            Filament::serving(function () {
                $user = auth()->user();
                if (! $user) return;

                $superRole = config('filament-modular-permissions.super_admin_role_name', 'super_admin');

                try {
                    if ($user->hasRole($superRole)) return; 
                } catch (\Throwable) {
                    return;
                }

                foreach (Filament::getPanels() as $panel) {
                    $guard = $panel->getAuthGuard();

                    try {
                        $ref  = new \ReflectionClass($panel);
                        $prop = null;
                        $cls  = $ref;

                        while ($cls) {
                            if ($cls->hasProperty('widgets')) {
                                $prop = $cls->getProperty('widgets');
                                $prop->setAccessible(true);
                                break;
                            }
                            $cls = $cls->getParentClass();
                        }

                        if (! $prop) continue;

                        $widgets = $prop->getValue($panel);

                        $filtered = array_values(array_filter($widgets, function ($widgetClass) use ($user, $guard) {
                            $snakeName  = Str::snake(class_basename($widgetClass));
                            $permission = "view_{$snakeName}";

                            try {
                                return $user->hasPermissionTo($permission, $guard);
                            } catch (\Throwable) {
                                return false;
                            }
                        }));

                        $prop->setValue($panel, $filtered);
                    } catch (\Throwable) {
                        continue;
                    }
                }
            });
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament()->getPlugin('filament-modular-permissions');

        return $plugin;
    }
}
