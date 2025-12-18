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
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('ticket_number')->unique();
            $table->string('event_name');
            $table->string('qr_code')->unique();
            $table->boolean('is_validated')->default(false);
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();

            $table->index('qr_code');
            $table->index('ticket_number');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
};

