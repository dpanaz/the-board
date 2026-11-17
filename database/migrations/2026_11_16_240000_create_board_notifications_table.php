<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained('board_members')->cascadeOnDelete();
            $table->string('category', 30);
            $table->string('title', 180);
            $table->string('body', 300)->nullable();
            $table->string('url', 255)->default('/');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['member_id', 'read_at', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_notifications');
    }
};
