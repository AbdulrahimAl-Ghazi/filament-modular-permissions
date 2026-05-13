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
    public function handle()
    {
        $actions = config('filament-modular-permissions.actions', [
            'view_any', 'view', 'create', 'update', 'delete', 'delete_any',
            'force_delete', 'force_delete_any', 'restore', 'restore_any',
            'reorder', 'replicate'
        ]);

        $panels = Filament::getPanels();
        $this->info("Found " . count($panels) . " Filament panels.");

        foreach ($panels as $panel) {
            $panelId = $panel->getId();
            $guard = $panel->getAuthGuard();
            $resources = $panel->getResources();

            $this->info("Processing Panel: {$panelId} (Guard: {$guard})");

            $superAdminRole = Role::firstOrCreate([
                'name' => 'super_admin',
                'guard_name' => $guard,
            ]);
            
            $this->info(" > Super Admin role created/verified for guard: {$guard}.");

            $permissionsCreatedCount = 0;

            foreach ($resources as $resource) {
                $resourceName = class_basename($resource);
                $snakeResourceName = Str::snake($resourceName);

                foreach ($actions as $action) {
                    $permissionName = "{$action}_{$snakeResourceName}";

                    Permission::firstOrCreate([
                        'name' => $permissionName,
                        'guard_name' => $guard,
                    ]);

                    $permissionsCreatedCount++;
                }
            }

            // Sync Widgets Permissions
            $widgets = $panel->getWidgets();
            foreach ($widgets as $widget) {
                $widgetName = class_basename($widget);
                $snakeWidgetName = Str::snake($widgetName);
                
                Permission::firstOrCreate([
                    'name' => "view_{$snakeWidgetName}",
                    'guard_name' => $guard,
                ]);

                $permissionsCreatedCount++;
            }

            $this->info(" > Synced {$permissionsCreatedCount} permissions (including widgets) for panel '{$panelId}'.");
        }

        $this->info("Permissions sync completed successfully.");
    }
}
