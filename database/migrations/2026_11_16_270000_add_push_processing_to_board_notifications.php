<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('board_notifications', function (Blueprint $table): void {
            $table->timestamp('push_processed_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('board_notifications', function (Blueprint $table): void {
            $table->dropColumn('push_processed_at');
        });
    }
};
