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
        Schema::create('soundtracks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->timestamps();
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('soundtrack_id')->constrained();
            $table->string('status')->default('pending');
            $table->string('path')->nullable();
            $table->string('webhook')->nullable();
            $table->timestamps();
        });

        Schema::create('video_images', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('video_id')->constrained();
            $table->string('path');
            $table->integer('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
