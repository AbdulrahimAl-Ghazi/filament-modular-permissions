<?php

namespace Abdulrahim\FilamentModularPermissions\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Support\Facades\File;

#[Signature('permissions:publish-lang {--force : Overwrite existing translation files} {--lang= : Publish a specific language only (e.g. en, ar)}')]
#[Description('Publish the Filament Modular Permissions translation files to your application.')]
class PublishLang extends Command
{
    public function handle(): int
    {
        $sourcePath      = __DIR__ . '/../../lang';
        $destinationPath = lang_path('vendor/filament-modular-permissions');
        $specificLang    = $this->option('lang');

        $this->newLine();
        $this->components->info('Publishing Translation Files');
        $this->newLine();

        if (! File::exists($sourcePath)) {
            $this->components->error('Language source directory not found in package.');
            return self::FAILURE;
        }

        $langDirs = File::directories($sourcePath);

        if (empty($langDirs)) {
            $this->components->warn('No language directories found in package.');
            return self::SUCCESS;
        }

        $published = 0;
        $skipped   = 0;

        foreach ($langDirs as $langDir) {
            $lang = basename($langDir);

            // If --lang is specified, only publish that language
            if ($specificLang && $lang !== $specificLang) {
                continue;
            }

            $destLangPath = $destinationPath . '/' . $lang;

            File::ensureDirectoryExists($destLangPath);

            foreach (File::files($langDir) as $file) {
                $destFile = $destLangPath . '/' . $file->getFilename();

                if (File::exists($destFile) && ! $this->option('force')) {
                    $this->line("  <fg=yellow>SKIP</>  {$lang}/{$file->getFilename()} <fg=gray>(already exists)</>");
                    $skipped++;
                    continue;
                }

                File::copy($file->getPathname(), $destFile);
                $this->line("  <fg=green>DONE</>  {$lang}/{$file->getFilename()}");
                $published++;
            }
        }

        $this->newLine();

        if ($published === 0 && $skipped > 0) {
            $this->components->warn("All files skipped. Use <fg=yellow>--force</> to overwrite existing translations.");
        } else {
            $this->components->info("Translation files published. ({$published} published, {$skipped} skipped)");

            if ($skipped > 0 && ! $this->option('force')) {
                $this->components->warn('Some files were skipped. Use <fg=yellow>--force</> to overwrite.');
            }
        }

        $this->newLine();

        return self::SUCCESS;
    }
}
