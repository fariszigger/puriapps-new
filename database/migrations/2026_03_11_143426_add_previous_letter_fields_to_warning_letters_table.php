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
        Schema::table('warning_letters', function (Blueprint $table) {
            $table->foreignId('previous_letter_id')->nullable()->constrained('warning_letters')->nullOnDelete();
            $table->string('previous_letter_number')->nullable();
            $table->date('previous_letter_date')->nullable();
            $table->decimal('previous_letter_amount', 15, 2)->nullable();
            $table->date('previous_letter_deadline')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('warning_letters', function (Blueprint $table) {
            $table->dropForeign(['previous_letter_id']);
            $table->dropColumn([
                'previous_letter_id',
                'previous_letter_number',
                'previous_letter_date',
                'previous_letter_amount',
                'previous_letter_deadline'
            ]);
        });
    }
};
