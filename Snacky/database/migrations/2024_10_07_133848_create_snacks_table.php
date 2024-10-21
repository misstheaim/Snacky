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
        Schema::create('snacks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('uzum_product_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('category_id')->nullable()->constrained('categories', 'uzum_category_id')->cascadeOnDelete();
            $table->string('title_ru')->nullable();
            $table->string('title_uz')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_uz')->nullable();
            $table->float('price')->nullable();
            $table->string('link');
            $table->string('low_image_link')->nullable();
            $table->string('high_image_link')->nullable();
            $table->enum('status', ['IN_PROCESS', 'APPROVED', 'DISAPPROVED']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snacks');
    }
};
