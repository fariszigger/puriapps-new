<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $t = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('app/public/kop-surat-with-text.docx'));
    echo "Variables:\n";
    print_r($t->getVariables());
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
