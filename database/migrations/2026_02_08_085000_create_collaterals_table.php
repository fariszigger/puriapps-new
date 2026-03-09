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
        Schema::create('collaterals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');

            $table->string('type'); // 'vehicle', 'certificate' (property), 'deposit'

            // --- Ownership & Proof (Common) ---
            $table->string('owner_name');
            $table->string('owner_ktp')->nullable(); // Nomor KTP Pemilik
            $table->string('proof_type'); // BPKB, SHM, SHGB, etc.
            $table->string('proof_number'); // Nomor Sertifikat / BPKB
            $table->string('proof_number_2')->nullable(); //Nomor Surat Ukur (Sertifikat)
            $table->date('proof_date')->nullable(); // Tanggal Kepemilikan
            $table->string('allocation')->nullable(); // Peruntukan / Penggunaan

            // --- Valuation (Common) ---
            $table->decimal('market_value', 15, 2)->default(0);
            $table->decimal('bank_value', 15, 2)->default(0); // Nilai Taksasi Bank

            // --- Vehicle Specifics (Nullable) ---
            $table->string('vehicle_brand')->nullable(); // Merk: Honda, Toyota...
            $table->string('vehicle_model')->nullable(); // Model: Brio, Avanza...
            $table->string('vehicle_plate_number')->nullable(); // No Pol
            $table->string('vehicle_year')->nullable(); // Tahun Pembuatan
            $table->string('vehicle_color')->nullable();
            $table->string('vehicle_frame_number')->nullable(); // No Rangka
            $table->string('vehicle_engine_number')->nullable(); // No Mesin
            $table->integer('vehicle_cc')->nullable(); // Kapasitas Mesin

            // --- Property/Certificate Specifics (Nullable) ---
            $table->text('property_address')->nullable();
            $table->decimal('property_surface_area', 10, 2)->nullable(); // Luas Tanah (m2)
            $table->decimal('property_building_area', 10, 2)->nullable(); // Luas Bangunan (m2)

            // --- Map Location ---
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->text('location_address')->nullable(); // For map pin address
            $table->string('village')->nullable(); // Kelurahan/Desa
            $table->string('district')->nullable(); // Kecamatan
            $table->string('regency')->nullable(); // Kabupaten/Kota
            $table->string('province')->nullable(); // Provinsi
            $table->decimal('path_distance', 10, 2)->nullable()->comment('Distance from collateral to office in km');
            $table->string('location_image')->nullable(); // Leaflet map capture image path

            // --- Images (Paths) ---
            $table->string('image_proof')->nullable(); // Foto Bukti Kepemilikan (BPKB/Sertifikat)
            $table->string('image_owner')->nullable(); // Foto Pemilik

            // Vehicle Images (Max 3)
            $table->string('vehicle_image_1')->nullable();
            $table->string('vehicle_image_2')->nullable();
            $table->string('vehicle_image_3')->nullable();
            $table->string('vehicle_image_4')->nullable();

            // Property Images (Max 4)
            $table->string('property_image_1')->nullable();
            $table->string('property_image_2')->nullable();
            $table->string('property_image_3')->nullable();
            $table->string('property_image_4')->nullable();

            // --- Tax Valuation (New) ---
            $table->string('tax_approach_method')->nullable(); // 'harga_pasar', 'pendekatan_biaya', etc.

            // JSON for valuation sources (dynamic rows)
            $table->string('tax_valuation_sources')->nullable();
            $table->decimal('tax_average_source_value', 15, 2)->default(0);

            $table->decimal('value_according_to_rule', 15, 2)->default(0);
            $table->decimal('value_according_to_liquidation', 15, 2)->default(0);

            $table->decimal('tax_final_value', 15, 2)->default(0); // Nilai Agunan Akhir

            $table->text('notes')->nullable(); // Spesifikasi Lainnya / Catatan

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collaterals');
    }
};
