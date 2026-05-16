<?php

namespace Abdulrahim\FilamentModularPermissions\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CheckUserPermissions extends Command
{
    protected $signature = 'permissions:check {--user= : The user ID to check permissions for} {--guard=web : The guard to check against}';

    protected $description = 'Diagnose and display all roles and permissions for a specific user.';

    public function handle(): int
    {
        $userId    = $this->option('user');
        $guard     = $this->option('guard');
        $superRole = config('filament-modular-permissions.super_admin_role_name', 'super_admin');

        // ── Validate user ID ─────────────────────────────────────────────
        if (! $userId) {
            $this->components->error('No user ID provided.');
            $this->line('  Usage: <fg=yellow>php artisan permissions:check --user=1 --guard=web</>');
            return self::FAILURE;
        }

        $userModel = config('auth.providers.users.model', \App\Models\User::class);
        $user      = $userModel::find($userId);

        if (! $user) {
            $this->components->error("User with ID [{$userId}] not found.");
            return self::FAILURE;
        }

        // ── Header ───────────────────────────────────────────────────────
        $this->newLine();
        $this->components->info('Permissions Diagnostic');
        $this->newLine();

        $this->line("  <fg=gray>User  :</> <fg=white;options=bold>{$user->name}</> <fg=gray>(ID: {$user->id})</>");
        $this->line("  <fg=gray>Email :</> {$user->email}");
        $this->line("  <fg=gray>Guard :</> {$guard}");
        $this->newLine();

        // ── Roles ────────────────────────────────────────────────────────
        $roles = $user->roles()->where('guard_name', $guard)->get();

        $this->line('<fg=blue;options=bold>Roles</>  <fg=gray>(' . $roles->count() . ')</>');

        if ($roles->isEmpty()) {
            $this->line('  <fg=yellow>None</>');
        } else {
            $this->table(
                ['Role', 'Guard', 'Super Admin'],
                $roles->map(fn ($role) => [
                    $role->name,
                    $role->guard_name,
                    $role->name === $superRole
                        ? '<fg=green>✓ Yes — full access</>'
                        : '<fg=gray>No</>',
                ])->toArray()
            );
        }

        // ── Direct Permissions ───────────────────────────────────────────
        $directPermissions = $user->getDirectPermissions()->where('guard_name', $guard);

        $this->line('<fg=blue;options=bold>Direct Permissions</>  <fg=gray>(' . $directPermissions->count() . ')</>');

        if ($directPermissions->isEmpty()) {
            $this->line('  <fg=gray>None</>');
            $this->newLine();
        } else {
            $this->table(
                ['Permission', 'Guard'],
                $directPermissions->map(fn ($p) => [$p->name, $p->guard_name])->toArray()
            );
        }

        // ── Permissions via Roles ────────────────────────────────────────
        $rolePermissions = $user->getPermissionsViaRoles()->where('guard_name', $guard);

        $this->line('<fg=blue;options=bold>Permissions via Roles</>  <fg=gray>(' . $rolePermissions->count() . ')</>');

        if ($rolePermissions->isEmpty()) {
            $this->line('  <fg=gray>None</>');
            $this->newLine();
        } else {
            $this->table(
                ['Permission', 'Guard', 'Via Role'],
                $rolePermissions->map(fn ($p) => [
                    $p->name,
                    $p->guard_name,
                    $p->pivot->role_id
                        ? (Role::find($p->pivot->role_id)?->name ?? '<fg=gray>unknown</>')
                        : '<fg=gray>unknown</>',
                ])->toArray()
            );
        }

        // ── Summary ──────────────────────────────────────────────────────
        $allCount    = $user->getAllPermissions()->where('guard_name', $guard)->count();
        $totalInDb   = Permission::where('guard_name', $guard)->count();
        $isSuperAdmin = $roles->contains('name', $superRole);

        $this->line('<fg=blue;options=bold>Summary</>');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Roles assigned',             $roles->count()],
                ['Direct permissions',          $directPermissions->count()],
                ['Permissions via roles',        $rolePermissions->count()],
                ['Total effective permissions',  $allCount],
                ['Total permissions in DB',      $totalInDb],
                [
                    'Super Admin status',
                    $isSuperAdmin
                        ? '<fg=green>✓ Full access (Gate::before bypasses all checks)</>'
                        : '<fg=gray>✗ Normal user — standard permission checks apply</>',
                ],
            ]
        );

        $this->newLine();

        return self::SUCCESS;
    }
}
