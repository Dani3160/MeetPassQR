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
        Schema::create('event_certificates', function (Blueprint $table) {
            $table->id('id_certificate');
            $table->foreignId('id_event')->constrained('events', 'id_event')->onDelete('cascade');
            $table->foreignId('id_template')->constrained('certificate_templates', 'id_template')->onDelete('restrict');
            
            // Field konfigurasi sertifikat
            $table->string('introductory_phrase', 255)->default('Sertifikat dengan bangga diberikan kepada');
            $table->string('completion_phrase', 255)->default('telah menyelesaikan praktek dari ebook');
            $table->string('organization_name', 100)->nullable();
            $table->string('signatory_name', 100)->nullable();
            $table->string('signatory_title', 100)->nullable();
            $table->string('signature_image', 255)->nullable(); // Path ke gambar tanda tangan
            $table->string('verification_url_base', 255)->nullable(); // Base URL untuk verifikasi
            $table->string('certificate_id_prefix', 20)->default('SK-'); // Prefix untuk ID sertifikat
            $table->boolean('auto_generate_id')->default(true); // Auto generate ID atau manual
            
            // JSON untuk field tambahan yang bisa dikustomisasi
            $table->json('custom_fields')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_certificates');
    }
};
