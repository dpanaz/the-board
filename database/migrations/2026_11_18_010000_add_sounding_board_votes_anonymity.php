<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('board_discussions', function (Blueprint $table) {
            $table->boolean('is_anonymous')->default(false);
        });
        Schema::create('board_discussion_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained('board_discussions')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('board_members')->cascadeOnDelete();
            $table->string('choice', 20);
            $table->timestamps();
            $table->unique(['discussion_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_discussion_votes');
        Schema::table('board_discussions', fn (Blueprint $table) => $table->dropColumn('is_anonymous'));
    }
};
