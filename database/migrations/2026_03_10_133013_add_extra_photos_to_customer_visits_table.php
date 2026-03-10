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
            $table->string('photo_rumah_path')->after('photo_path')->nullable();
            $table->string('photo_orang_path')->after('photo_rumah_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->dropColumn(['photo_rumah_path', 'photo_orang_path']);
        });
    }
};
