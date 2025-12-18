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
        Schema::create('event_field_orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->unsignedBigInteger('id_event');
            $table->enum('form_type', ['add', 'edit', 'complete_profile'])->default('add');
            $table->string('field_type', 50); // 'default' or 'custom'
            $table->string('field_key', 100); // field name like 'guest_name', 'guest_email', or custom field id
            $table->integer('field_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
            
            $table->foreign('id_event')->references('id_event')->on('events')->onDelete('cascade');
            $table->unique(['id_event', 'form_type', 'field_type', 'field_key'], 'unique_field_order');
            $table->index(['id_event', 'form_type', 'field_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_field_orders');
    }
};
