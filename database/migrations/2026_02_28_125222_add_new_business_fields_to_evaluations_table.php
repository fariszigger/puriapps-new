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
            $table->integer('customer_dependents')->nullable();
            $table->text('customer_entreprenuership_description')->nullable();
            $table->string('customer_entreprenuership_products')->nullable();
            $table->string('customer_entreprenuership_place_status')->nullable();
            $table->string('customer_entreprenuership_phone')->nullable();
            $table->string('customer_entreprenuership_employee_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'customer_dependents',
                'customer_entreprenuership_description',
                'customer_entreprenuership_products',
                'customer_entreprenuership_place_status',
                'customer_entreprenuership_phone',
                'customer_entreprenuership_employee_count'
            ]);
        });
    }
};
