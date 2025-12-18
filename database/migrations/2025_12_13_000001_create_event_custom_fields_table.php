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
        Schema::create('event_custom_fields', function (Blueprint $table) {
            $table->id('id_field');
            $table->unsignedBigInteger('id_event');
            $table->string('field_name', 100);
            $table->string('field_label', 255);
            $table->enum('field_type', ['input', 'textarea', 'file', 'select', 'radio', 'checkbox']);
            $table->text('field_options')->nullable()->comment('JSON untuk select/radio/checkbox options');
            $table->boolean('is_required')->default(false);
            $table->integer('field_order')->default(0);
            $table->string('field_placeholder', 255)->nullable();
            $table->text('field_validation')->nullable()->comment('JSON validation rules');
            $table->timestamps();
            
            $table->foreign('id_event')->references('id_event')->on('events')->onDelete('cascade');
            $table->index(['id_event', 'field_order'], 'idx_event_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_custom_fields');
    }
};
