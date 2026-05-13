<?php

namespace Abdulrahim\FilamentModularPermissions\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ModularPermissionsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $panel = Filament::getCurrentPanel();
        if (!$panel) {
            return $next($request);
        }

        $user = Filament::auth()->user();
        if (!$user) {
            return $next($request);
        }

        // 1. Grant everything to super_admin immediately
        $superAdminRole = config('filament-modular-permissions.super_admin_role_name', 'super_admin');
        if ($user->hasRole($superAdminRole)) {
            return $next($request);
        }

        // 2. Identify the current resource from the route
        // Filament routes for resources usually follow: filament.{panel_id}.resources.{resource_name}.{action}
        $routeName = $request->route()?->getName();
        
        if ($routeName && Str::contains($routeName, '.resources.')) {
            $parts = explode('.', $routeName);
            $resourceIndex = array_search('resources', $parts);
            
            if ($resourceIndex !== false && isset($parts[$resourceIndex + 1])) {
                $resourceSlug = $parts[$resourceIndex + 1];
                $actionPart = $parts[$resourceIndex + 2] ?? 'index';
                
                // Map Filament route actions to our permission actions
                $actionMap = [
                    'index' => 'view_any',
                    'create' => 'create',
                    'edit' => 'update',
                    'view' => 'view',
                ];

                $action = $actionMap[$actionPart] ?? 'view_any';
                
                // Convert slug to resource name (e.g., users -> user_resource)
                $resourceName = Str::snake(Str::singular($resourceSlug)) . '_resource';
                
                $permissionName = "{$action}_{$resourceName}";

                if (!$user->can($permissionName)) {
                    abort(403, "You do not have permission to access {$resourceSlug} ({$permissionName}).");
                }
            }
        }

        return $next($request);
    }
}
