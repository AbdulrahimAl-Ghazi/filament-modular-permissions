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

class FilamentModularPermissionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'filament-modular-permissions');

        // Note: Global Authorization and Widget Protection logic 
        // has been moved to FilamentModularPermissionsPlugin::boot()
        // for better integration with Filament's panel system.

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

            // Publishing translations
            $this->publishes([
                __DIR__ . '/../lang' => lang_path('vendor/filament-modular-permissions'),
            ], 'filament-modular-permissions-lang');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/filament-modular-permissions.php',
            'filament-modular-permissions'
        );
    }
}
