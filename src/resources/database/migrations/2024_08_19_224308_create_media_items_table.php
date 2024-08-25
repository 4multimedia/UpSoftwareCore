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
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('primary_id')->nullable();
            $table->unsignedBigInteger('directory_id')->nullable();
            $table->string('file_name');
            $table->string('file_name_original');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->json('file_info')->nullable();

            $table->foreign('primary_id')->references('id')->on('media_items');
            $table->foreign('directory_id')->references('id')->on('media_directories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_items');
    }
};
