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
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->boolean('is_manual_exclude_bayar')->default(false)->after('jumlah_bayar');
            $table->unsignedBigInteger('manual_exclude_by')->nullable()->after('is_manual_exclude_bayar');
            $table->foreign('manual_exclude_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->dropForeign(['manual_exclude_by']);
            $table->dropColumn(['is_manual_exclude_bayar', 'manual_exclude_by']);
        });
    }
};
