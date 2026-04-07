<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->comment('AO who owns this disbursement');
            $table->string('customer_name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('disbursement_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_disbursements');
    }
};
