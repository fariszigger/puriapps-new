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
            // Capital Scoring
            $table->integer('capital_dar')->default(0);
            $table->integer('capital_der')->default(0);
            $table->decimal('capital_total_score', 5, 2)->default(0);

            // Condition Scoring
            $table->integer('cond_lokasi')->default(0);
            $table->integer('cond_profit')->default(0);
            $table->integer('cond_dscr')->default(0);
            $table->decimal('condition_total_score', 5, 2)->default(0);

            // Collateral Scoring
            $table->integer('col_kepemilikan')->default(0);
            $table->integer('col_peruntukan')->default(0);
            $table->integer('col_lebar_jalan')->default(0);
            $table->integer('col_coverage')->default(0);
            $table->integer('col_marketable')->default(0);
            $table->decimal('col_total_score', 5, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn([
                'capital_dar', 'capital_der', 'capital_total_score',
                'cond_lokasi', 'cond_profit', 'cond_dscr', 'condition_total_score',
                'col_kepemilikan', 'col_peruntukan', 'col_lebar_jalan', 'col_coverage', 'col_marketable', 'col_total_score'
            ]);
        });
    }
};
