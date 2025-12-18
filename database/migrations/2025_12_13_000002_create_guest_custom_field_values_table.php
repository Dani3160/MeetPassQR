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
        Schema::create('guest_custom_field_values', function (Blueprint $table) {
            $table->id('id_value');
            $table->unsignedBigInteger('id_guest');
            $table->unsignedBigInteger('id_field');
            $table->text('field_value')->nullable()->comment('Nilai untuk input/textarea/select/radio/checkbox');
            $table->string('file_path', 255)->nullable()->comment('Path file untuk type file');
            $table->timestamps();
            
            $table->foreign('id_guest')->references('id_guest')->on('guests')->onDelete('cascade');
            $table->foreign('id_field')->references('id_field')->on('event_custom_fields')->onDelete('cascade');
            $table->unique(['id_guest', 'id_field'], 'unique_guest_field');
            $table->index('id_guest', 'idx_guest');
            $table->index('id_field', 'idx_field');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_custom_field_values');
    }
};
