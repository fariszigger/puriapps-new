<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view credit-disbursements',
            'create credit-disbursements',
            'edit credit-disbursements',
            'delete credit-disbursements',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Give permissions to Admin and Kabag by default
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo($permissions);
        }

        $kabag = Role::where('name', 'Kabag')->first();
        if ($kabag) {
            $kabag->givePermissionTo($permissions);
        }

        // View only for Direksi and AO
        $direksi = Role::where('name', 'Direksi')->first();
        if ($direksi) {
            $direksi->givePermissionTo(['view credit-disbursements']);
        }

        $ao = Role::where('name', 'AO')->first();
        if ($ao) {
            $ao->givePermissionTo(['view credit-disbursements']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view credit-disbursements',
            'create credit-disbursements',
            'edit credit-disbursements',
            'delete credit-disbursements',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};
