<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth()->login($user);

$req = Illuminate\Http\Request::create('/warning-letters/1', 'GET');
$response = app()->handle($req);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Headers: \n";
foreach ($response->headers->all() as $name => $values) {
    echo "  $name: " . implode(', ', $values) . "\n";
}

if (method_exists($response, 'getFile')) {
    $file = $response->getFile();
    echo "File path inside response: " . $file->getPathname() . "\n";
    echo "File exists? " . (file_exists($file->getPathname()) ? 'Yes' : 'No') . "\n";
    if (file_exists($file->getPathname())) {
        echo "File size: " . filesize($file->getPathname()) . "\n";
    }
}
