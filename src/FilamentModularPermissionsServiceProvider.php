<?php

namespace Abdulrahim\FilamentModularPermissions;

use Illuminate\Support\ServiceProvider;
use Abdulrahim\FilamentModularPermissions\Commands\PublishRoleResources;
use Abdulrahim\FilamentModularPermissions\Commands\PublishUserResource;
use Abdulrahim\FilamentModularPermissions\Commands\SyncPanelPermissions;
use Abdulrahim\FilamentModularPermissions\Commands\InstallPermissions;
use Abdulrahim\FilamentModularPermissions\Commands\CheckUserPermissions;
use Abdulrahim\FilamentModularPermissions\Commands\PublishConfig;
use Abdulrahim\FilamentModularPermissions\Commands\PublishLang;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Filament\Facades\Filament;

class FilamentModularPermissionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'filament-modular-permissions');

        // Global Authorization Logic
        Gate::before(function ($user, $ability, $args) {
            // 1. Super Admin Always Allowed
            $superAdminRole = config('filament-modular-permissions.super_admin_role_name', 'super_admin');
            
            try {
                if ($user->hasRole($superAdminRole)) {
                    return true;
                }
            } catch (\Throwable $e) {
                return null;
            }

            // 2. Global Auto-Hiding Logic
            if (config('filament-modular-permissions.auto_hide_resources', true)) {
                
                $abilityMap = [
                    'viewAny' => 'view_any',
                    'view' => 'view',
                    'create' => 'create',
                    'update' => 'update',
                    'delete' => 'delete',
                    'deleteAny' => 'delete_any',
                    'forceDelete' => 'force_delete',
                    'forceDeleteAny' => 'force_delete_any',
                    'restore' => 'restore',
                    'restoreAny' => 'restore_any',
                    'reorder' => 'reorder',
                    'replicate' => 'replicate',
                ];

                // Check if it's a model authorization (Filament Resources)
                // $args[0] can be a class string (viewAny/create) OR a model instance (update/delete/etc)
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

        // ── Auto Widget Protection ───────────────────────────────────────────
        // Filament does NOT call Gate for widgets — it calls Widget::canView() directly.
        // So we filter the widget list per-panel in serving() before any rendering occurs.
        if (config('filament-modular-permissions.auto_hide_resources', true)) {
            Filament::serving(function () {
                $user = auth()->user();
                if (! $user) return;

                $superRole = config('filament-modular-permissions.super_admin_role_name', 'super_admin');

                try {
                    if ($user->hasRole($superRole)) return; // Super admin sees everything
                } catch (\Throwable) {
                    return;
                }

                foreach (Filament::getPanels() as $panel) {
                    $guard = $panel->getAuthGuard();

                    try {
                        // Access the raw widgets list via reflection
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
                                // Permission doesn't exist in DB → deny
                                return false;
                            }
                        }));

                        $prop->setValue($panel, $filtered);
                    } catch (\Throwable) {
                        // If anything fails, do nothing (fail open for safety)
                        continue;
                    }
                }
            });
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishRoleResources::class,
                PublishUserResource::class,
                SyncPanelPermissions::class,
                InstallPermissions::class,
                CheckUserPermissions::class,
                PublishConfig::class,
                PublishLang::class,
            ]);

            // Publishing config
            $this->publishes([
                __DIR__ . '/../config/filament-modular-permissions.php' => config_path('filament-modular-permissions.php'),
            ], 'filament-modular-permissions-config');

            // Publishing stubs
            $this->publishes([
                __DIR__ . '/../stubs' => base_path('stubs/filament-modular-permissions'),
            ], 'filament-modular-permissions-stubs');

            // Publishing translations
            $this->publishes([
                __DIR__ . '/../lang' => lang_path('vendor/filament-modular-permissions'),
            ], 'filament-modular-permissions-lang');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/filament-modular-permissions.php', 'filament-modular-permissions'
        );
    }
}
