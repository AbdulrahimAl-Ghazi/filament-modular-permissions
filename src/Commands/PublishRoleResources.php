<?php

namespace Abdulrahim\FilamentModularPermissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

#[Description('Publish Filament Role Resource and schemas to a specific panel')]
#[Signature('permissions:publish-resources {--panel= : The ID of the panel to publish to} {--force : Overwrite existing files}')]
class PublishRoleResources extends Command
{
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
        $namespace     = "App\\Filament\\{$panelName}\\Resources\\Roles";
        $resourcesPath = app_path("Filament/{$panelName}/Resources/Roles");

        if ($panelId === 'admin' && ! File::exists(app_path('Filament/Admin'))) {
            $namespace     = 'App\\Filament\\Resources\\Roles';
            $resourcesPath = app_path('Filament/Resources/Roles');
        }

        $customStubPath = base_path('stubs/filament-modular-permissions/Roles');
        $packageStubPath = __DIR__ . '/../../stubs/Roles';
        $stubPath = File::exists($customStubPath) ? $customStubPath : $packageStubPath;
        $usingCustomStubs = File::exists($customStubPath);

        $this->newLine();
        $this->components->info("Publishing Role Resources → Panel: <fg=white>{$panelId}</>");
        $this->line("  <fg=gray>Namespace:</> {$namespace}");
        $this->line("  <fg=gray>Stubs:</>     " . ($usingCustomStubs ? '<fg=yellow>custom (project stubs)</>' : '<fg=gray>package defaults</>'));
        $this->newLine();

        $this->createDirectoryIfNotExists($resourcesPath);
        $this->createDirectoryIfNotExists($resourcesPath . '/Pages');
        $this->createDirectoryIfNotExists($resourcesPath . '/Schemas');
        $this->createDirectoryIfNotExists($resourcesPath . '/Tables');

        $vars = [
            'namespace'        => $namespace,
            'panel_id'         => $panelId,
            'guard'            => $panel->getAuthGuard(),
            'navigation_group' => config('filament-modular-permissions.role_resource.navigation_group', 'Settings'),
            'navigation_icon'  => config('filament-modular-permissions.role_resource.navigation_icon', 'heroicon-o-shield-check'),
        ];

        $files = [
            $stubPath . '/RoleResource.stub'         => $resourcesPath . '/RoleResource.php',
            $stubPath . '/Pages/ListRoles.stub'      => $resourcesPath . '/Pages/ListRoles.php',
            $stubPath . '/Pages/CreateRole.stub'     => $resourcesPath . '/Pages/CreateRole.php',
            $stubPath . '/Pages/EditRole.stub'       => $resourcesPath . '/Pages/EditRole.php',
            $stubPath . '/Schemas/RoleForm.stub'     => $resourcesPath . '/Schemas/RoleForm.php',
            $stubPath . '/Tables/RolesTable.stub'    => $resourcesPath . '/Tables/RolesTable.php',
        ];

        $published = 0;
        $skipped   = 0;

        foreach ($files as $stub => $destination) {
            $result = $this->publishFile($stub, $destination, $vars);
            $result === 'published' ? $published++ : $skipped++;
        }

        $this->newLine();
        $this->components->info("Role Resources published. ({$published} published, {$skipped} skipped)");

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
