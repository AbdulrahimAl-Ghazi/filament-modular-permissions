<?php

namespace Abdulrahim\FilamentModularPermissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Support\Facades\File;

#[Signature('permissions:publish-config {--force : Overwrite existing config file}')]
#[Description('Publish the Filament Modular Permissions config file to your application.')]
class PublishConfig extends Command
{
    public function handle(): int
    {
        $source      = __DIR__ . '/../../config/filament-modular-permissions.php';
        $destination = config_path('filament-modular-permissions.php');

        $this->newLine();
        $this->components->info('Publishing Config File');
        $this->newLine();

        if (File::exists($destination) && ! $this->option('force')) {
            $this->line('  <fg=yellow>SKIP</>  filament-modular-permissions.php <fg=gray>(already exists — use --force to overwrite)</>');
            $this->newLine();
            $this->components->warn('Config file was not published. Use <fg=yellow>--force</> to overwrite.');
            $this->newLine();
            return self::SUCCESS;
        }

        File::copy($source, $destination);

        $this->line('  <fg=green>DONE</>  filament-modular-permissions.php → <fg=gray>config/filament-modular-permissions.php</>');
        $this->newLine();
        $this->components->info('Config file published successfully.');
        $this->newLine();

        return self::SUCCESS;
    }
}
