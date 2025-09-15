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
        Schema::create('imageables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('image_id');
            $table->morphs('imageable'); // imageable_id, imageable_type
            $table->string('type')->default('gallery'); // thumbnail, gallery, banner, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false); // Hình ảnh chính
            $table->timestamps();

            // Indexes
            $table->index(['imageable_type', 'imageable_id'], 'imageables_imageable_index');
            $table->index(['image_id', 'type'], 'imageables_image_type_index');
            $table->index('is_primary', 'imageables_is_primary_index');

            // Foreign key
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imageables');
    }
};
