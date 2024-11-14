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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('status', ['SEEN', 'NOT_SEEN'])->nullable()->default('NOT_SEEN');
            $table->enum('type', ['APPROVED', 'REJECTED', 'ADDED_TO_THE_RECEIPT', 'SUBMISSION'])->nullable();
            $table->unsignedBigInteger('snack_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('snack_id')->references('id')->on('snacks')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
