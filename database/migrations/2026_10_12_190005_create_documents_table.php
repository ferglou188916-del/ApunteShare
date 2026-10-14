<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_area_id')->constrained()->restrictOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('document_type_id')->constrained()->restrictOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('university', 160)->nullable();
            $table->string('career', 160)->nullable();
            $table->string('academic_cycle', 30)->nullable();
            $table->string('topic', 160)->nullable();
            $table->string('original_filename', 255);
            $table->string('file_path', 500);
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('file_size');
            $table->string('status', 25)->default('published');
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'academic_area_id', 'document_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
