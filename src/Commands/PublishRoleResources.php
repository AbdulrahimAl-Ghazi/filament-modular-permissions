<?php

namespace Abdulrahim\FilamentModularPermissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

#[Description('Publish Filament resources for Spatie Roles and Permissions with modular structure')]
#[Signature('permissions:publish-resources {--panel= : The ID of the panel to publish to}')]
class PublishRoleResources extends Command
{
    public function handle()
    {
        $panelId = $this->option('panel');
        
        $namespace = config('filament-modular-permissions.generator.namespace', 'App\\Filament\\Resources\\Roles');
        $resourcesPath = config('filament-modular-permissions.generator.path', app_path('Filament/Resources/Roles'));

        // If a panel is specified, we try to guess the path and namespace
        if ($panelId) {
            $panel = Filament::getPanel($panelId);
            if (!$panel) {
                $this->error("Panel [{$panelId}] not found.");
                return;
            }

            // Guess namespace based on panel provider's namespace
            // Typically Panels are in App\Providers\Filament\AdminPanelProvider
            // We want App\Filament\Admin\Resources\Roles
            $panelName = Str::studly($panelId);
            $namespace = "App\\Filament\\{$panelName}\\Resources\\Roles";
            $resourcesPath = app_path("Filament/{$panelName}/Resources/Roles");
        }
        
        $customStubPath = base_path('stubs/filament-modular-permissions/Roles');
        $packageStubPath = __DIR__ . '/../../stubs/Roles';
        
        $stubPath = File::exists($customStubPath) ? $customStubPath : $packageStubPath;

        $this->info("Publishing to: {$namespace}");
        $this->info("Target Path: {$resourcesPath}");

        $this->createDirectoryIfNotExists($resourcesPath);
        $this->createDirectoryIfNotExists($resourcesPath . '/Pages');
        $this->createDirectoryIfNotExists($resourcesPath . '/Schemas');
        $this->createDirectoryIfNotExists($resourcesPath . '/Tables');

        $vars = [
            'namespace' => $namespace,
            'navigation_group' => config('filament-modular-permissions.role_resource.navigation_group', 'Settings'),
            'navigation_icon' => config('filament-modular-permissions.role_resource.navigation_icon', 'heroicon-o-shield-check'),
            'navigation_label' => config('filament-modular-permissions.role_resource.navigation_label', 'Roles & Permissions'),
        ];

        $this->publishFile($stubPath . '/RoleResource.stub', $resourcesPath . '/RoleResource.php', $vars);
        $this->publishFile($stubPath . '/Pages/ListRoles.stub', $resourcesPath . '/Pages/ListRoles.php', $vars);
        $this->publishFile($stubPath . '/Pages/CreateRole.stub', $resourcesPath . '/Pages/CreateRole.php', $vars);
        $this->publishFile($stubPath . '/Pages/EditRole.stub', $resourcesPath . '/Pages/EditRole.php', $vars);
        $this->publishFile($stubPath . '/Schemas/RoleForm.stub', $resourcesPath . '/Schemas/RoleForm.php', $vars);
        $this->publishFile($stubPath . '/Tables/RolesTable.stub', $resourcesPath . '/Tables/RolesTable.php', $vars);

        $this->info('Modular Roles and Permissions resource has been published successfully.');
    }

    protected function createDirectoryIfNotExists($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    protected function publishFile($stub, $destination, $vars)
    {
        if (!File::exists($stub)) {
            $this->error("Stub not found: {$stub}");
            return;
        }

        $content = File::get($stub);
        
        foreach ($vars as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value ?? '', $content);
        }

        File::put($destination, $content);
        $this->line("Published: " . class_basename($destination));
    }
}
