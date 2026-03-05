<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Consolidates opening_cash/savings/giro into kas_usaha/piutang_usaha/persediaan
     */
    public function up(): void
    {
        // First, migrate any existing data from opening_* columns to kas_usaha/piutang_usaha/persediaan
        // Only update rows where kas_usaha is 0 (not yet set) but opening_cash_balance has data
        DB::statement("
            UPDATE evaluations 
            SET kas_usaha = opening_cash_balance,
                piutang_usaha = opening_savings_balance,
                persediaan = opening_giro_balance
            WHERE (kas_usaha = 0 OR kas_usaha IS NULL)
              AND (opening_cash_balance != 0 OR opening_savings_balance != 0 OR opening_giro_balance != 0)
        ");

        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'opening_cash_balance',
                'opening_savings_balance',
                'opening_giro_balance',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->decimal('opening_cash_balance', 15, 2)->default(0)->after('rpc_ratio');
            $table->decimal('opening_savings_balance', 15, 2)->default(0)->after('opening_cash_balance');
            $table->decimal('opening_giro_balance', 15, 2)->default(0)->after('opening_savings_balance');
        });

        // Restore data from kas_usaha/piutang_usaha/persediaan
        DB::statement("
            UPDATE evaluations 
            SET opening_cash_balance = kas_usaha,
                opening_savings_balance = piutang_usaha,
                opening_giro_balance = persediaan
        ");
    }
};
