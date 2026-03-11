<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$templatePath = storage_path('app/public/kop-surat.docx');

if (!file_exists($templatePath)) {
    echo "File not found at $templatePath\n";
    exit;
}

try {
    $template = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
    echo "Variables found in template:\n";
    print_r($template->getVariables());
    
    // Create a test file
    $template->setValue('nomor_surat', 'TEST-001/BPR.PURI.KRD/III/2026');
    $testOut = storage_path('app/public/test_output.docx');
    $template->saveAs($testOut);
    
    echo "Successfully saved test file to: $testOut\n";
    echo "Filesize: " . filesize($testOut) . " bytes\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
