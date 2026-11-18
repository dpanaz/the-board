<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('board_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('board_members')->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('category', 40);
            $table->text('body');
            $table->text('outcome')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['created_at']);
        });
        Schema::create('board_discussion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained('board_discussions')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('board_members')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
            $table->index(['discussion_id', 'created_at']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('board_discussion_replies');
        Schema::dropIfExists('board_discussions');
    }
};
