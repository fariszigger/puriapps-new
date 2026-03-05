<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->decimal('jumlah_pembayaran', 15, 2)->nullable()->after('tanggal_janji_bayar');
        });
    }

    public function down(): void
    {
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->dropColumn('jumlah_pembayaran');
        });
    }
};
