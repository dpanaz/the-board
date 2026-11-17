<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_meeting_reminders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('meeting_id')->constrained('board_meetings')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('board_members')->cascadeOnDelete();
            $table->unsignedSmallInteger('minutes_before');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['meeting_id', 'member_id', 'minutes_before'], 'board_reminder_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_meeting_reminders');
    }
};
