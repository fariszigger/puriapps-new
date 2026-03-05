<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->string('customer_employee_status')->nullable()->after('customer_employment_status');
            $table->string('customer_company_sector')->nullable()->after('customer_company_name');
            $table->string('customer_company_employee_count')->nullable()->after('customer_company_sector');
            $table->date('customer_company_payday')->nullable()->after('customer_company_employee_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'customer_employee_status',
                'customer_company_sector',
                'customer_company_employee_count',
                'customer_company_payday'
            ]);
        });
    }
};
