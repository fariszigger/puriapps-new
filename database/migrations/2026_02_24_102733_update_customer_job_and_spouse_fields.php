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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('job')->nullable()->after('phone_number');
            $table->string('spouse_job')->nullable()->after('spouse_relation');
            $table->string('spouse_education')->nullable()->after('spouse_job');
            $table->string('spouse_notelp')->nullable()->after('spouse_education');
            
            $table->dropColumn(['relation', 'last_financing_ceiling', 'credit_quality', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['job', 'spouse_job', 'spouse_education', 'spouse_notelp']);
            
            $table->string('relation')->nullable();
            $table->decimal('last_financing_ceiling', 15, 2)->default(0);
            $table->string('credit_quality')->nullable();
            $table->text('description')->nullable();
        });
    }
};
