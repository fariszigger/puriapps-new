<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warning_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('user_id')->constrained(); // Created by

            $table->enum('type', ['sp1', 'sp2', 'sp3', 'panggilan']);
            $table->string('letter_number')->nullable();          // Nomor surat: 117/BPR.PURI.KRD/X/2025
            $table->date('letter_date');                           // Tanggal surat

            // Credit info
            $table->string('credit_agreement_number')->nullable(); // No. Perjanjian Kredit / SPK
            $table->date('credit_agreement_date')->nullable();     // Tanggal Perjanjian Kredit

            // Tunggakan
            $table->decimal('tunggakan_amount', 15, 2)->nullable(); // Jumlah tunggakan
            $table->date('tunggakan_date')->nullable();              // Posisi tanggal tunggakan

            // Deadline
            $table->date('deadline_date')->nullable();  // Paling lambat tanggal

            // Snapshot from visits
            $table->string('kolektibilitas')->nullable();
            $table->integer('penagihan_ke')->nullable();

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warning_letters');
    }
};
