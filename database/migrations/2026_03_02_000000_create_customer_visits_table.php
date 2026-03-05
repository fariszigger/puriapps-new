<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('user_id')->constrained();

            // Address & Location
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('village')->nullable();
            $table->string('district')->nullable();
            $table->string('regency')->nullable();
            $table->string('province')->nullable();

            // Visit Details
            $table->string('kolektibilitas'); // 1-5
            $table->string('ketemu_dengan'); // Debitur, Suami/Istri, etc.
            $table->string('nama_orang_ditemui')->nullable();
            $table->decimal('baki_debet', 15, 2)->nullable(); // Outstanding balance for KL/Diragukan/Macet
            $table->longText('kondisi_saat_ini')->nullable();
            $table->longText('rencana_penyelesaian')->nullable();

            // Collection Results
            $table->string('hasil_penagihan')->nullable(); // bayar / janji_bayar
            $table->decimal('jumlah_bayar', 15, 2)->nullable();
            $table->date('tanggal_janji_bayar')->nullable();

            // Media
            $table->string('photo_path')->nullable();
            $table->string('location_image_path')->nullable();

            // Auto-calculated
            $table->integer('penagihan_ke')->default(1);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_visits');
    }
};
