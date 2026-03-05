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
            $table->decimal('kas_usaha', 15, 2)->default(0)->after('profit_past_after');
            $table->decimal('piutang_usaha', 15, 2)->default(0)->after('kas_usaha');
            $table->decimal('persediaan', 15, 2)->default(0)->after('piutang_usaha');
            $table->decimal('kewajiban_lancar', 15, 2)->default(0)->after('persediaan');
            $table->decimal('kewajiban_jangka_panjang', 15, 2)->default(0)->after('kewajiban_lancar');
            $table->decimal('modal_usaha', 15, 2)->default(0)->after('kewajiban_jangka_panjang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'kas_usaha',
                'piutang_usaha',
                'persediaan',
                'kewajiban_lancar',
                'kewajiban_jangka_panjang',
                'modal_usaha'
            ]);
        });
    }
};
