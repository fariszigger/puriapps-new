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
        Schema::create('customer_external_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->string('bank_name');
            $table->enum('collectibility', ['Lancar', 'DPK', 'Kurang Lancar', 'Diragukan', 'Macet'])->default('Lancar');
            $table->date('realization_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->decimal('original_amount', 15, 2)->default(0);
            $table->decimal('outstanding_balance', 15, 2)->default(0);
            $table->integer('term_months')->default(0);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->string('interest_method')->default('Flat'); // Flat, Anuitas, Efektif
            $table->decimal('installment_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_external_loans');
    }
};
