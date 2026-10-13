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
        Schema::create('location_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_link_id')->constrained('location_links')->cascadeOnDelete();
            $table->double('latitude', 11, 8);
            $table->double('longitude', 11, 8);
            $table->double('accuracy')->nullable();
            $table->double('altitude')->nullable();
            $table->double('speed')->nullable();
            $table->double('heading')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_visits');
    }
};
