<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title_ru')->nullable();
            $table->string('title_uz')->nullable();
            $table->unsignedBigInteger('uzum_category_id')->unique()->nullable();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->bigInteger('parent_id');
            // $table->foreign('parent_id')->references('uzum_category_id')->on('categories')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
