<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create permissions
        $permissions = [
            'view customers',
            'create customers',
            'update customers',
            'delete customers',
            'view evaluations',
            'create evaluations',
            'update evaluations',
            'delete evaluations',
            'approve evaluations',
            'view customer-visits',
            'create customer-visits',
            'update customer-visits',
            'delete customer-visits',
            'manage users',
            'manage roles',
            'manage gps',
            'view all data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Create roles and assign permissions
        $rolePermissions = [
            'Admin' => $permissions, // All permissions
            'Kabag' => [
                'view customers', 'create customers', 'update customers',
                'view evaluations', 'create evaluations', 'update evaluations', 'approve evaluations',
                'view customer-visits', 'create customer-visits', 'update customer-visits',
                'view all data',
            ],
            'AO' => [
                'view customers', 'create customers', 'update customers',
                'view evaluations', 'create evaluations', 'update evaluations',
                'view customer-visits', 'create customer-visits', 'update customer-visits',
            ],
            'Direksi' => [
                'view customers',
                'view evaluations',
                'view customer-visits',
                'view all data',
            ],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

        // 3. Migrate existing users from column to Spatie roles
        if (Schema::hasColumn('users', 'role')) {
            $users = \App\Models\User::all();
            foreach ($users as $user) {
                $roleName = $user->getAttributes()['role'] ?? null;
                if ($roleName && Role::where('name', $roleName)->exists()) {
                    $user->assignRole($roleName);
                }
            }

            // 4. Drop the old role column
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }

    public function down(): void
    {
        // Re-add role column
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('AO')->after('username');
            });

            // Copy Spatie roles back to column
            $users = \App\Models\User::with('roles')->get();
            foreach ($users as $user) {
                $roleName = $user->roles->first()?->name ?? 'AO';
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $user->id)
                    ->update(['role' => $roleName]);
            }
        }

        // Remove role assignments (but keep roles/permissions for re-migration)
    }
};
