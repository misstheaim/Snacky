<?php

use App\Models\Notification;
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
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('type', ['APPROVED', 'REJECTED', 'ADDED_TO_THE_RECEIPT', 'SUBMISSION', 'COMMENTED'])->change()->nullable();
            $table->unsignedBigInteger('comment_id')->nullable();

            $table->foreign('comment_id')->references('id')->on('filament_comments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Notification::where('type', 'COMMENTED')->delete();
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('type', ['APPROVED', 'REJECTED', 'ADDED_TO_THE_RECEIPT', 'SUBMISSION'])->change()->nullable();

            $table->dropForeign(['comment_id']);
            $table->dropColumn('comment_id');
        });
    }
};
