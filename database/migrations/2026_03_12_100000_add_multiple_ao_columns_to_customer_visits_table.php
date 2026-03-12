<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->boolean('is_accompanying')->default(false)->after('spk_number');
            $table->string('accompanying_names')->nullable()->after('is_accompanying');
        });
    }

    public function down(): void
    {
        Schema::table('customer_visits', function (Blueprint $table) {
            $table->dropColumn('is_accompanying');
            $table->dropColumn('accompanying_names');
        });
    }
};
