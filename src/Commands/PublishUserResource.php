<?php

namespace Abdulrahim\FilamentModularPermissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

#[Description('Publish Filament User Resource for panel management')]
#[Signature('permissions:publish-user-resource {--panel= : The ID of the panel to publish to}')]
class PublishUserResource extends Command
{
    public function handle()
    {
        $panelId = $this->option('panel');
        
        // Interactive panel selection if not provided
        if (!$panelId) {
            $panels = array_keys(Filament::getPanels());
            if (count($panels) > 1) {
                $panelId = $this->choice('Which panel would you like to publish to?', $panels, $panels[0]);
            } else {
                $panelId = $panels[0] ?? null;
            }
        }

        if (!$panelId) {
            $this->error('No panels found in your application.');
            return;
        }

        $panel = Filament::getPanel($panelId);
        if (!$panel) {
            $this->error("Panel [{$panelId}] not found.");
            return;
        }

        $panelName = Str::studly($panelId);
        $namespace = "App\\Filament\\{$panelName}\\Resources\\Users";
        $resourcesPath = app_path("Filament/{$panelName}/Resources/Users");

        // Default path if it's the main admin panel and follows standard convention
        if ($panelId === 'admin' && !File::exists(app_path('Filament/Admin'))) {
            $namespace = 'App\\Filament\\Resources\\Users';
            $resourcesPath = app_path('Filament/Resources/Users');
        }
        
        $customStubPath = base_path('stubs/filament-modular-permissions/Users');
        $packageStubPath = __DIR__ . '/../../stubs/Users';
        
        $stubPath = File::exists($customStubPath) ? $customStubPath : $packageStubPath;

        $this->info("Publishing User Resource to: {$namespace}");

        $this->createDirectoryIfNotExists($resourcesPath);
        $this->createDirectoryIfNotExists($resourcesPath . '/Pages');
        $this->createDirectoryIfNotExists($resourcesPath . '/Schemas');
        $this->createDirectoryIfNotExists($resourcesPath . '/Tables');

        $vars = [
            'namespace' => $namespace,
            'navigation_group' => config('filament-modular-permissions.user_resource.navigation_group', 'Settings'),
            'navigation_icon' => config('filament-modular-permissions.user_resource.navigation_icon', 'heroicon-o-users'),
            'navigation_label' => config('filament-modular-permissions.user_resource.navigation_label', 'Users'),
        ];

        $this->publishFile($stubPath . '/UserResource.stub', $resourcesPath . '/UserResource.php', $vars);
        $this->publishFile($stubPath . '/Pages/ListUsers.stub', $resourcesPath . '/Pages/ListUsers.php', $vars);
        $this->publishFile($stubPath . '/Pages/CreateUser.stub', $resourcesPath . '/Pages/CreateUser.php', $vars);
        $this->publishFile($stubPath . '/Pages/EditUser.stub', $resourcesPath . '/Pages/EditUser.php', $vars);
        $this->publishFile($stubPath . '/Schemas/UserForm.stub', $resourcesPath . '/Schemas/UserForm.php', $vars);
        $this->publishFile($stubPath . '/Tables/UsersTable.stub', $resourcesPath . '/Tables/UsersTable.php', $vars);

        $this->info('Modular User Resource has been published successfully.');
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
