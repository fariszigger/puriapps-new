<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure warning-letter permissions exist (they may have been added via /fix-permissions)
        Permission::firstOrCreate(['name' => 'view warning-letters', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create warning-letters', 'guard_name' => 'web']);

        // Create the Pelayanan role with scoped permissions
        $role = Role::firstOrCreate(['name' => 'Pelayanan', 'guard_name' => 'web']);

        $role->syncPermissions([
            // Nasabah: view + create (no update/delete)
            'view customers',
            'create customers',

            // Kunjungan: view only
            'view customer-visits',

            // Surat: view + create (create permission covers store/update in WarningLetterController)
            'view warning-letters',
            'create warning-letters',
        ]);
    }

    public function down(): void
    {
        $role = Role::where('name', 'Pelayanan')->first();
        if ($role) {
            $role->delete();
        }
    }
};
