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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            // Basic Info
            $table->string('name');
            $table->string('type')->nullable(); // Perorangan / Badan
            $table->string('identity_number')->nullable(); // KTP / NPWP
            $table->string('pob')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();

            // Contact & Address
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('village')->nullable();
            $table->string('district')->nullable();
            $table->string('regency')->nullable();
            $table->string('province')->nullable();

            // Family Background
            $table->string('mother_name')->nullable();
            $table->string('education')->nullable();
            $table->string('emergency_contact')->nullable();

            // Spouse Information
            $table->string('spouse_name')->nullable();
            $table->string('spouse_identity_number')->nullable();
            $table->string('spouse_pob')->nullable();
            $table->date('spouse_dob')->nullable();
            $table->string('spouse_relation')->nullable();
            $table->text('spouse_description')->nullable();

            // Relationship & Financing
            $table->string('relation')->nullable();
            $table->decimal('last_financing_ceiling', 15, 2)->default(0);
            $table->string('credit_quality')->nullable();
            $table->text('description')->nullable();

            // Files & Location
            $table->string('photo_path')->nullable();
            $table->string('document_path')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_image_path')->nullable();
            $table->decimal('path_distance', 10, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
