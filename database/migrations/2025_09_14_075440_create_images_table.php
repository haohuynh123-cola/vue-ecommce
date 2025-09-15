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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('filename'); // Tên file gốc
            $table->string('original_name'); // Tên file ban đầu
            $table->string('path'); // Đường dẫn lưu trữ
            $table->string('url'); // URL đầy đủ để truy cập
            $table->string('mime_type'); // Loại file (image/jpeg, image/png, etc.)
            $table->unsignedBigInteger('file_size'); // Kích thước file (bytes)
            $table->string('type')->default('gallery'); // thumbnail, gallery, banner, avatar, etc.
            $table->string('alt_text')->nullable(); // Alt text cho SEO
            $table->text('description')->nullable(); // Mô tả hình ảnh
            $table->json('metadata')->nullable(); // Metadata bổ sung (dimensions, exif, etc.)
            $table->boolean('is_public')->default(true); // Có public không
            $table->unsignedBigInteger('uploaded_by')->nullable(); // User upload
            $table->string('disk')->default('public'); // Storage disk
            $table->integer('sort_order')->default(0); // Thứ tự sắp xếp
            $table->timestamps();

            // Indexes
            $table->index(['type', 'is_public']);
            $table->index('uploaded_by');
            $table->index('sort_order');

            // Foreign key
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
