<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_area_id')->constrained()->restrictOnDelete();
            $table->string('name', 160);
            $table->string('code', 40)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['academic_area_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
