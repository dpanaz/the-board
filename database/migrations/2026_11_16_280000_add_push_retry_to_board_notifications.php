<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('board_notifications', function (Blueprint $table): void {
            $table->unsignedTinyInteger('push_attempts')->default(0);
            $table->timestamp('push_next_attempt_at')->nullable()->index();
            $table->text('push_last_error')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('board_notifications', function (Blueprint $table): void {
            $table->dropColumn(['push_attempts', 'push_next_attempt_at', 'push_last_error']);
        });
    }
};
