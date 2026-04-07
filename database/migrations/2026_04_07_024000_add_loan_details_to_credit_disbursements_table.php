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
        Schema::table('credit_disbursements', function (Blueprint $table) {
            $table->integer('jangka_waktu')->default(0)->after('amount')->comment('Dalam bulan');
            $table->decimal('suku_bunga', 5, 2)->default(0)->after('jangka_waktu')->comment('Persentase per tahun');
            $table->enum('jenis_pinjaman', ['flat', 'anuitas', 'musiman'])->default('flat')->after('suku_bunga');
            $table->decimal('angsuran', 15, 2)->default(0)->after('jenis_pinjaman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_disbursements', function (Blueprint $table) {
            $table->dropColumn(['jangka_waktu', 'suku_bunga', 'jenis_pinjaman', 'angsuran']);
        });
    }
};
