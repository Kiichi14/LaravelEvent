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
        Schema::create('event_location', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('location_id');

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');

            $table->string('theater');
            $table->integer('place_number');
            $table->date('date');

            $table->index(['event_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_location');
    }
};
