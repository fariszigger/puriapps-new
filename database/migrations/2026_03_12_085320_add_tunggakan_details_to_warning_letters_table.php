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
        Schema::table('warning_letters', function (Blueprint $table) {
            $table->decimal('tunggakan_pokok', 15, 2)->nullable()->after('tunggakan_amount');
            $table->decimal('tunggakan_bunga', 15, 2)->nullable()->after('tunggakan_pokok');
            $table->decimal('denda_keterlambatan', 15, 2)->nullable()->after('tunggakan_bunga');
            $table->decimal('previous_tunggakan_pokok', 15, 2)->nullable()->after('previous_letter_amount');
            $table->decimal('previous_tunggakan_bunga', 15, 2)->nullable()->after('previous_tunggakan_pokok');
            $table->decimal('previous_denda_keterlambatan', 15, 2)->nullable()->after('previous_tunggakan_bunga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warning_letters', function (Blueprint $table) {
            $table->dropColumn([
                'tunggakan_pokok',
                'tunggakan_bunga',
                'denda_keterlambatan',
                'previous_tunggakan_pokok',
                'previous_tunggakan_bunga',
                'previous_denda_keterlambatan'
            ]);
        });
    }
};
