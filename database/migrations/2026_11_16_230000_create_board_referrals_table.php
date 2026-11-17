<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_referrals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('from_member_id')->constrained('board_members')->cascadeOnDelete();
            $table->foreignId('to_member_id')->constrained('board_members')->cascadeOnDelete();
            $table->string('subject', 180);
            $table->text('details')->nullable();
            $table->string('status', 20)->default('new');
            $table->timestamps();
            $table->index(['to_member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_referrals');
    }
};
