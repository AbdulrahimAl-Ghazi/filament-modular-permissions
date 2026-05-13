<?php

namespace Abdulrahim\FilamentModularPermissions\Traits;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Str;
use UnitEnum;

trait HandlesResourcePermissions
{
    /**
     * Override Filament's default authorization to rely on Resource-based permissions
     * instead of Model-based policies.
     */
    public static function getAuthorizationResponse(string | UnitEnum $action, ?Model $record = null): Response
    {
        $actionName = $action instanceof UnitEnum ? $action->name : $action;
        
        // Convert camelCase action (e.g., viewAny) to snake_case (e.g., view_any)
        $snakeAction = Str::snake($actionName);
        
        // Use Resource class name instead of Model name (e.g., UserResource -> user_resource)
        $resourceName = class_basename(static::class);
        $snakeResource = Str::snake($resourceName);
        
        $permissionName = "{$snakeAction}_{$snakeResource}";
        
        $user = Filament::auth()->user();
        
        if ($user && $user->can($permissionName)) {
            return Response::allow();
        }

        return Response::deny("You do not have permission to {$snakeAction} this resource ({$snakeResource}).");
    }
}
