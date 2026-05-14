<?php

namespace Abdulrahim\FilamentModularPermissions\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Signature('permissions:sync')]
#[Description('Sync all permissions and super admin role from Filament panels based on resources and guards.')]
class SyncPanelPermissions extends Command
{
    public function handle(): int
    {
        $actions = config('filament-modular-permissions.actions', [
            'view_any', 'view', 'create', 'update', 'delete', 'delete_any',
            'force_delete', 'force_delete_any', 'restore', 'restore_any',
            'reorder', 'replicate',
        ]);

        $customPermissions = config('filament-modular-permissions.custom_permissions', []);
        $panels            = Filament::getPanels();

        $this->newLine();
        $this->components->info('Syncing permissions across ' . count($panels) . ' panel(s)...');
        $this->newLine();

        $grandTotal = 0;

        foreach ($panels as $panel) {
            $panelId   = $panel->getId();
            $guard     = $panel->getAuthGuard();
            $resources = $panel->getResources();
            $widgets   = $panel->getWidgets();

            $this->line("  <fg=blue;options=bold>Panel:</> <fg=white>{$panelId}</> <fg=gray>(guard: {$guard})</>");

            // ── Super Admin Role ─────────────────────────────────────────
            $roleCreated = ! Role::where(['name' => 'super_admin', 'guard_name' => $guard])->exists();
            Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => $guard]);
            $roleStatus  = $roleCreated ? '<fg=green>created</>' : '<fg=gray>exists</>';
            $this->line("  <fg=gray>›</> super_admin role …… {$roleStatus}");

            // ── Resources ────────────────────────────────────────────────
            $resourceCount = 0;
            foreach ($resources as $resource) {
                $resourceName = str_replace('Resource', '', class_basename($resource));
                $snakeName = Str::snake($resourceName);
                foreach ($actions as $action) {
                    Permission::firstOrCreate(['name' => "{$action}_{$snakeName}", 'guard_name' => $guard]);
                    $resourceCount++;
                }
            }

            // ── Widgets ──────────────────────────────────────────────────
            $widgetCount = 0;
            foreach ($widgets as $widget) {
                $snakeName = Str::snake(class_basename($widget));
                Permission::firstOrCreate(['name' => "view_{$snakeName}", 'guard_name' => $guard]);
                $widgetCount++;
            }

            // ── Custom Permissions ───────────────────────────────────────
            $customCount = 0;
            foreach ($customPermissions as $perm) {
                Permission::firstOrCreate(['name' => $perm, 'guard_name' => $guard]);
                $customCount++;
            }

            $panelTotal = $resourceCount + $widgetCount + $customCount;
            $grandTotal += $panelTotal;

            $this->table(
                ['Type', 'Count'],
                [
                    ['Resources permissions', $resourceCount],
                    ['Widget permissions',    $widgetCount],
                    ['Custom permissions',    $customCount],
                    ['<fg=white;options=bold>Total for panel</>',    "<fg=white;options=bold>{$panelTotal}</>"],
                ],
            );
            $this->newLine();
        }

        $this->components->info("Done! {$grandTotal} permission(s) are now registered in the database.");
        $this->newLine();

        return self::SUCCESS;
    }
}
