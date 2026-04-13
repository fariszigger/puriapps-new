<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::controller(\App\Http\Controllers\AuthController::class)->group(function () {
    Route::get('/login', 'index')->name('login')->middleware('guest');
    Route::post('/login', 'login')->name('login.post')->middleware('guest');
    Route::get('/captcha-refresh', 'refreshCaptcha')->name('captcha.refresh');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware(['authentication'])->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'stats'])->name('dashboard.stats');

    Route::get('/customers/{customer}/print', [App\Http\Controllers\CustomerController::class, 'print'])->name('customers.print');
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::get('/evaluations/{evaluation}/print', [App\Http\Controllers\EvaluationController::class, 'print'])->name('evaluations.print');
    Route::post('/evaluations/{evaluation}/approval', [\App\Http\Controllers\EvaluationController::class, 'processApproval'])->name('evaluations.approval');
    Route::post('/evaluations/{evaluation}/send', [\App\Http\Controllers\EvaluationController::class, 'sendForApproval'])->name('evaluations.send');
    Route::post('/evaluations/{evaluation}/revoke', [\App\Http\Controllers\EvaluationController::class, 'revokeApproval'])->name('evaluations.revoke');
    Route::resource('evaluations', \App\Http\Controllers\EvaluationController::class);
    Route::get('/geocoding/reverse', [\App\Http\Controllers\GeocodingController::class, 'reverse'])->name('geocoding.reverse');
    Route::get('/geocoding/search', [\App\Http\Controllers\GeocodingController::class, 'search'])->name('geocoding.search');

    Route::get('/global-map', [\App\Http\Controllers\MapController::class, 'index'])->name('map.index');

    // Warning Letters
    Route::resource('warning-letters', App\Http\Controllers\WarningLetterController::class);

    // Credit Disbursements
    Route::get('/credit-disbursements/print', [App\Http\Controllers\CreditDisbursementController::class, 'print'])->name('credit-disbursements.print');
    Route::get('/credit-disbursements/export', [App\Http\Controllers\CreditDisbursementController::class, 'export'])->name('credit-disbursements.export');
    Route::get('/credit-disbursements/analytics', [App\Http\Controllers\CreditDisbursementController::class, 'analytics'])->name('credit-disbursements.analytics');
    Route::resource('credit-disbursements', App\Http\Controllers\CreditDisbursementController::class);
    
    // Collection History
    Route::get('/collection-history', [App\Http\Controllers\CollectionHistoryController::class, 'index'])->name('collection-history.index');
    Route::get('/collection-history/{customer}/print', [App\Http\Controllers\CollectionHistoryController::class, 'print'])->name('collection-history.print');

    Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar/toggle-promise/{customerVisit}', [\App\Http\Controllers\CalendarController::class, 'togglePromise'])->name('calendar.toggle-promise');
    Route::get('/calendar/recap', [\App\Http\Controllers\CalendarController::class, 'recap'])->name('calendar.recap');

    Route::get('/reports/performance', [ReportController::class, 'performance'])->name('reports.performance');
    Route::get('/reports/performance/recap', [ReportController::class, 'recap'])->name('reports.performance-recap');
    Route::get('/reports/performance/{user}/detail', [ReportController::class, 'detail'])->name('reports.performance-detail');

    Route::get('/customer-visits/count/{customerId}', [\App\Http\Controllers\CustomerVisitController::class, 'count'])->name('customer-visits.count');
    Route::get('/customer-visits/{id}/report', [\App\Http\Controllers\CustomerVisitController::class, 'report'])->name('customer-visits.report');
    Route::resource('customer-visits', \App\Http\Controllers\CustomerVisitController::class);

    Route::controller(\App\Http\Controllers\ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
    });

    // Admin only routes
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('roles', \App\Http\Controllers\RoleController::class);
        Route::resource('gps-trackers', \App\Http\Controllers\GpsTrackerController::class);
        Route::post('/evaluations/{evaluation}/restore', [\App\Http\Controllers\EvaluationController::class, 'restore'])->name('evaluations.restore');
        Route::post('/customers/{customer}/restore', [\App\Http\Controllers\CustomerController::class, 'restore'])->name('customers.restore');
    });
});

Route::get('/media/customers/{type}/{filename}', [\App\Http\Controllers\MediaController::class, 'serveCustomerMedia'])
    ->name('media.customers');

Route::get('/media/evaluations/{type}/{filename}', [\App\Http\Controllers\MediaController::class, 'serveEvaluationMedia'])
    ->name('media.evaluations');

Route::get('/media/customer-visits/{type}/{filename}', [\App\Http\Controllers\MediaController::class, 'serveCustomerVisitMedia'])
    ->name('media.customer-visits');

Route::get('/fix-permissions', function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    
    $p = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view performance reports', 'guard_name' => 'web']);
    \Spatie\Permission\Models\Role::findByName('Admin')->givePermissionTo($p);
    \Spatie\Permission\Models\Role::findByName('Kabag')->givePermissionTo($p);
    \Spatie\Permission\Models\Role::findByName('Direksi')->givePermissionTo($p);

    $pRestoreCustomers = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'restore customers', 'guard_name' => 'web']);
    \Spatie\Permission\Models\Role::findByName('Admin')->givePermissionTo($pRestoreCustomers);
    
    return 'Permissions fixed and cache cleared successfully! You can refresh your dashboard now.';
});
