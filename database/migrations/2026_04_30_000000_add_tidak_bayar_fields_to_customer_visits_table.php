<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->boolean('janji_bayar_tidak_bayar')->default(false)->after('jumlah_bayar_fulfilled');
            $table->text('janji_bayar_tidak_bayar_reason')->nullable()->after('janji_bayar_tidak_bayar');
            $table->timestamp('janji_bayar_tidak_bayar_at')->nullable()->after('janji_bayar_tidak_bayar_reason');
        });
    }

    public function down(): void
    {
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->dropColumn(['janji_bayar_tidak_bayar', 'janji_bayar_tidak_bayar_reason', 'janji_bayar_tidak_bayar_at']);
        });
    }
};
