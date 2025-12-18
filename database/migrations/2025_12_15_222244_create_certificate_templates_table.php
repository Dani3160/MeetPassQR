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
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id('id_template');
            $table->string('template_name', 100);
            $table->text('template_description')->nullable();
            $table->text('html_structure'); // HTML template dengan placeholders
            $table->text('css_styles'); // CSS untuk styling template
            $table->string('preview_image', 255)->nullable(); // Path ke preview image
            $table->json('configurable_fields')->nullable(); // Field yang bisa dikonfigurasi
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
