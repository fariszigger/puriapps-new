<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('credit_scoring_components', function (Blueprint $table) {
            $table->id();
            $table->string('loan_scheme');
            $table->string('category'); // character, capacity, capital, condition, collateral
            $table->string('name'); // e.g. "SLIK", "Repayment Capacity"
            $table->integer('weight')->default(0); // Default weight percentage
            $table->integer('max_score')->default(5); // Default max score (usually 5)
            $table->boolean('is_active')->default(true); // To "remove" (hide) from frontend without deleting history
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_scoring_components');
    }
};
