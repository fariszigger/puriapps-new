<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->boolean('janji_bayar_fulfilled')->default(false)->after('tanggal_janji_bayar');
            $table->timestamp('janji_bayar_fulfilled_at')->nullable()->after('janji_bayar_fulfilled');
        });
    }

    public function down(): void
    {
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->dropColumn(['janji_bayar_fulfilled', 'janji_bayar_fulfilled_at']);
        });
    }
};
