<?php

namespace Abdulrahim\FilamentModularPermissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

class PublishUserResource extends Command
{
    protected $signature = 'permissions:publish-user-resource {--panel= : The ID of the panel to publish to} {--force : Overwrite existing files}';

    protected $description = 'Publish Filament User Resource for panel management';

    public function handle(): int
    {
        $panelId = $this->option('panel');

        // Interactive panel selection if not provided
        if (! $panelId) {
            $panels = array_keys(Filament::getPanels());
            if (count($panels) > 1) {
                $panelId = $this->choice('Which panel would you like to publish to?', $panels, $panels[0]);
            } else {
                $panelId = $panels[0] ?? null;
            }
        }

        if (! $panelId) {
            $this->components->error('No panels found in your application.');
            return self::FAILURE;
        }

        $panel = Filament::getPanel($panelId);
        if (! $panel) {
            $this->components->error("Panel [{$panelId}] not found.");
            return self::FAILURE;
        }

        $panelName     = Str::studly($panelId);
        $namespace     = "App\\Filament\\{$panelName}\\Resources\\Users";
        $resourcesPath = app_path("Filament/{$panelName}/Resources/Users");

        if ($panelId === 'admin' && ! File::exists(app_path('Filament/Admin'))) {
            $namespace     = 'App\\Filament\\Resources\\Users';
            $resourcesPath = app_path('Filament/Resources/Users');
        }

        $customStubPath  = base_path('stubs/filament-modular-permissions/Users');
        $packageStubPath = __DIR__ . '/../../stubs/Users';
        $stubPath        = File::exists($customStubPath) ? $customStubPath : $packageStubPath;
        $usingCustomStubs = File::exists($customStubPath);

        $this->newLine();
        $this->components->info("Publishing User Resource → Panel: <fg=white>{$panelId}</>");
        $this->line("  <fg=gray>Namespace:</> {$namespace}");
        $this->line("  <fg=gray>Stubs:</>     " . ($usingCustomStubs ? '<fg=yellow>custom (project stubs)</>' : '<fg=gray>package defaults</>'));
        $this->newLine();

        $this->createDirectoryIfNotExists($resourcesPath);
        $this->createDirectoryIfNotExists($resourcesPath . '/Pages');
        $this->createDirectoryIfNotExists($resourcesPath . '/Schemas');
        $this->createDirectoryIfNotExists($resourcesPath . '/Tables');

        $vars = [
            'namespace'        => $namespace,
            'navigation_group' => config('filament-modular-permissions.user_resource.navigation_group', 'Settings'),
            'navigation_icon'  => config('filament-modular-permissions.user_resource.navigation_icon', 'heroicon-o-users'),
            'navigation_label' => config('filament-modular-permissions.user_resource.navigation_label', 'Users'),
        ];

        $files = [
            $stubPath . '/UserResource.stub'        => $resourcesPath . '/UserResource.php',
            $stubPath . '/Pages/ListUsers.stub'     => $resourcesPath . '/Pages/ListUsers.php',
            $stubPath . '/Pages/CreateUser.stub'    => $resourcesPath . '/Pages/CreateUser.php',
            $stubPath . '/Pages/EditUser.stub'      => $resourcesPath . '/Pages/EditUser.php',
            $stubPath . '/Schemas/UserForm.stub'    => $resourcesPath . '/Schemas/UserForm.php',
            $stubPath . '/Tables/UsersTable.stub'   => $resourcesPath . '/Tables/UsersTable.php',
        ];

        $published = 0;
        $skipped   = 0;

        foreach ($files as $stub => $destination) {
            $result = $this->publishFile($stub, $destination, $vars);
            $result === 'published' ? $published++ : $skipped++;
        }

        $this->newLine();
        $this->components->info("User Resource published. ({$published} published, {$skipped} skipped)");

        if ($skipped > 0 && ! $this->option('force')) {
            $this->components->warn('Some files were skipped because they already exist. Use <fg=yellow>--force</> to overwrite.');
        }

        $this->newLine();

        return self::SUCCESS;
    }

    protected function createDirectoryIfNotExists(string $path): void
    {
        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    protected function publishFile(string $stub, string $destination, array $vars): string
    {
        $filename = class_basename($destination);

        if (! File::exists($stub)) {
            $this->components->error("Stub not found: {$stub}");
            return 'skipped';
        }

        if (File::exists($destination) && ! $this->option('force')) {
            $this->line("  <fg=yellow>SKIP</>  {$filename}");
            return 'skipped';
        }

        $content = File::get($stub);
        foreach ($vars as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value ?? '', $content);
        }

        File::put($destination, $content);
        $this->line("  <fg=green>DONE</>  {$filename}");

        return 'published';
    }
}
