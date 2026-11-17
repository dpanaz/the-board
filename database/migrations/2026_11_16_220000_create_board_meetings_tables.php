<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_meetings', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 180);
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->foreignId('created_by')->constrained('board_members')->cascadeOnDelete();
            $table->timestamps();
            $table->index('starts_at');
        });

        Schema::create('board_meeting_rsvps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('meeting_id')->constrained('board_meetings')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('board_members')->cascadeOnDelete();
            $table->string('response', 16);
            $table->timestamps();
            $table->unique(['meeting_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_meeting_rsvps');
        Schema::dropIfExists('board_meetings');
    }
};
