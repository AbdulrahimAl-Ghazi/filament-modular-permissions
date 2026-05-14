<?php

namespace Abdulrahim\FilamentModularPermissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('permissions:install {--panel= : The ID of the panel to install to} {--force : Overwrite existing files} {--skip-user : Skip publishing the User Resource}')]
#[Description('Install Filament Modular Permissions: sync permissions and publish all resources in one step.')]
class InstallPermissions extends Command
{
    public function handle(): void
    {
        $this->newLine();
        $this->components->info('Filament Modular Permissions — Installer');
        $this->newLine();

        $publishArgs = array_filter([
            '--panel' => $this->option('panel') ?: null,
            '--force' => $this->option('force') ?: null,
        ]);

        // ── Step 1: Role Resources ───────────────────────────────────────
        $this->components->task(
            '<fg=cyan>Step 1/3</> Publishing Role Management Resource',
            fn () => $this->callSilently('permissions:publish-resources', $publishArgs) === 0
        );

        // ── Step 2: User Resource ────────────────────────────────────────
        if (! $this->option('skip-user')) {
            $this->components->task(
                '<fg=cyan>Step 2/3</> Publishing User Management Resource',
                fn () => $this->callSilently('permissions:publish-user-resource', $publishArgs) === 0
            );
        } else {
            $this->components->warn('Step 2/3 skipped — User Resource not published (--skip-user).');
        }

        // ── Step 3: Sync ────────────────────────────────────────────────
        $this->components->task(
            '<fg=cyan>Step 3/3</> Syncing all permissions (including newly published resources)',
            fn () => $this->callSilently('permissions:sync') === 0
        );

        $this->newLine();
        $this->components->info('Installation complete!');
        $this->newLine();

        $this->components->bulletList([
            'Register the published Resources in your Filament panel.',
            'Run <fg=yellow>php artisan db:seed</> to create your first super_admin user.',
            'Ensure your User model uses the <fg=yellow>HasRoles</> trait from Spatie.',
            'Re-run <fg=yellow>php artisan permissions:sync</> whenever you add new Resources or Widgets.',
        ]);

        $this->newLine();
    }
}
