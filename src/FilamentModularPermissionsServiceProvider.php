<?php

namespace Abdulrahim\FilamentModularPermissions;

use Illuminate\Support\ServiceProvider;
use Abdulrahim\FilamentModularPermissions\Commands\PublishRoleResources;
use Abdulrahim\FilamentModularPermissions\Commands\PublishUserResource;
use Abdulrahim\FilamentModularPermissions\Commands\SyncPanelPermissions;
use Abdulrahim\FilamentModularPermissions\Commands\InstallPermissions;
use Abdulrahim\FilamentModularPermissions\Commands\CheckUserPermissions;
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
            $roleName = config('filament-modular-permissions.super_admin_role_name', 'super_admin');
            
            try {
                if ($user->hasRole($roleName)) {
                    return true;
                }
            } catch (\Throwable $e) {
                // If role check fails (e.g. Spatie tables missing), let it pass to other policies
                return null;
            }

            // 2. Global Auto-Hiding Logic (for Resources & Navigation)
            if (config('filament-modular-permissions.auto_hide_resources', true)) {
                
                // Map Filament standard actions to our modular permissions
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
                if (isset($abilityMap[$ability]) && isset($args[0]) && is_string($args[0]) && class_exists($args[0])) {
                    if (is_subclass_of($args[0], 'Illuminate\Database\Eloquent\Model')) {
                        $modelName = Str::snake(class_basename($args[0]));
                        $permission = "{$abilityMap[$ability]}_{$modelName}";

                        try {
                            if ($user->hasPermissionTo($permission)) {
                                return true;
                            }
                        } catch (\Throwable $e) {
                            // Permission doesn't exist in DB, handle gracefully
                            return null;
                        }

                        return null;
                    }
                }

                // Check if it's a widget authorization (Custom Permission Check)
                if (str_starts_with($ability, 'view_') && str_ends_with($ability, '_widget')) {
                    try {
                        if ($user->hasPermissionTo($ability)) {
                            return true;
                        }
                    } catch (\Throwable $e) {
                        return null;
                    }

                    return null;
                }
            }

            return null; // Let other policies handle it if not caught
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishRoleResources::class,
                PublishUserResource::class,
                SyncPanelPermissions::class,
                InstallPermissions::class,
                CheckUserPermissions::class,
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
