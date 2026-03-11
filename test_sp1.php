<?php

use App\Models\Customer;
use App\Models\CustomerVisit;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$q1 = Customer::whereHas('visits', function ($q) {
    $q->whereIn('kolektibilitas', ['3', '4', '5']);
})->whereHas('visits', function ($q) {}, '>', 3)->toSql();

echo "Query SQL:\n" . $q1 . "\n\n";

$customers = Customer::whereHas('visits', function ($q) {
    $q->whereIn('kolektibilitas', ['3', '4', '5']);
})->whereHas('visits', function ($q) {}, '>', 3)->get();

echo "Matched Customers: " . $customers->count() . "\n";
foreach ($customers as $c) {
    echo "- " . $c->name . "\n";
}

$allVisitsCount = CustomerVisit::selectRaw('customer_id, count(*) as c')->groupBy('customer_id')->get();
echo "\nAll Visit Counts:\n";
foreach ($allVisitsCount as $v) {
    echo "Customer " . $v->customer_id . ": " . $v->c . " visits\n";
}

$kolekVisits = CustomerVisit::whereIn('kolektibilitas', ['3', '4', '5'])->get();
echo "\nBad Kolektibilitas Visits: " . $kolekVisits->count() . "\n";
foreach ($kolekVisits as $v) {
    echo "Customer " . $v->customer_id . " - Kolek " . $v->kolektibilitas . "\n";
}
