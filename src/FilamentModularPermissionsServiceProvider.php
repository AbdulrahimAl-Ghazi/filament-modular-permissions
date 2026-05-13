<?php

namespace Abdulrahim\FilamentModularPermissions;

use Illuminate\Support\ServiceProvider;
use Abdulrahim\FilamentModularPermissions\Commands\PublishRoleResources;
use Abdulrahim\FilamentModularPermissions\Commands\SyncPanelPermissions;
use Illuminate\Support\Facades\Gate;

class FilamentModularPermissionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'filament-modular-permissions');

        // Grant all permissions to super_admin
        Gate::before(function ($user, $ability) {
            $roleName = config('filament-modular-permissions.super_admin_role_name', 'super_admin');
            return $user->hasRole($roleName) ? true : null;
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishRoleResources::class,
                SyncPanelPermissions::class,
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
