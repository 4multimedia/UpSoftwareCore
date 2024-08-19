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
        Schema::create('model_has_medias', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('media_item_id');
            $table->unsignedBigInteger('model_id');
            $table->string('model_type');
            $table->boolean('is_main')->default(false);
            $table->boolean('status')->default(true);
            $table->integer('position')->default(0);

            $table->foreign('media_item_id')->references('id')->on('media_items');

            $table->index(['media_item_id', 'model_id', 'model_type'], 'model_has_medias_model_id_model_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_medias');
    }
};
