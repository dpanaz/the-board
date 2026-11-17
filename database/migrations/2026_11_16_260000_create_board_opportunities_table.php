<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_opportunities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->constrained('board_members')->cascadeOnDelete();
            $table->string('type', 10);
            $table->string('title', 180);
            $table->text('details');
            $table->string('status', 12)->default('open');
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_opportunities');
    }
};
