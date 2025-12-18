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
        Schema::create('guests', function (Blueprint $table) {
            $table->id('id_guest');
            $table->unsignedBigInteger('id_event');
            $table->unsignedBigInteger('id_session')->nullable()->default(null);
            $table->string('guest_title', 255)->nullable();
            $table->string('guest_name', 100);
            $table->string('guest_address', 100);
            $table->string('guest_email', 100)->nullable();
            $table->string('guest_phone', 20)->nullable();
            $table->integer('guest_label')->default(0);
            $table->boolean('guest_status')->default(false);
            $table->timestamp('guest_time_arrival')->default('1970-01-02 00:00:00');
            $table->timestamp('guest_time_leave')->default('1970-01-02 00:00:00');
            $table->string('guest_pic', 255)->default('avatar.png');
            $table->timestamp('guest_created_at')->useCurrent();
            $table->timestamps();
            
            $table->foreign('id_event')->references('id_event')->on('events')->onDelete('cascade');
            $table->foreign('id_session')->references('id_session')->on('event_sessions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
