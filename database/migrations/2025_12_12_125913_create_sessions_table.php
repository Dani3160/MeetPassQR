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
        Schema::create('event_sessions', function (Blueprint $table) {
            $table->id('id_session');
            $table->unsignedBigInteger('id_event');
            $table->string('name_session', 255)->nullable();
            $table->time('time_started_session');
            $table->time('time_ended_session');
            $table->timestamps();
            
            $table->foreign('id_event')->references('id_event')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sessions');
    }
};
