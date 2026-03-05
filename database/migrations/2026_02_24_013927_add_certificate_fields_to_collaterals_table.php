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
        Schema::table('collaterals', function (Blueprint $table) {
            $table->string('peruntukan_tanah')->nullable();
            $table->decimal('lebar_jalan', 8, 2)->nullable();
            $table->string('kondisi_bangunan')->nullable();
            $table->string('material_pondasi')->nullable();
            $table->string('material_tembok')->nullable();
            $table->string('material_atap')->nullable();
            $table->string('material_kusen')->nullable();
            $table->string('material_daun_pintu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn([
                'peruntukan_tanah',
                'lebar_jalan',
                'kondisi_bangunan',
                'material_pondasi',
                'material_tembok',
                'material_atap',
                'material_kusen',
                'material_daun_pintu'
            ]);
        });
    }
};
