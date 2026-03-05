<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$p = Permission::firstOrCreate(['guard_name' => 'web', 'name' => 'view performance reports']);

$admin = Role::findByName('Admin');
if ($admin) $admin->givePermissionTo($p);

$kabag = Role::findByName('Kabag');
if ($kabag) $kabag->givePermissionTo($p);

echo "Permission created and assigned!\n";
