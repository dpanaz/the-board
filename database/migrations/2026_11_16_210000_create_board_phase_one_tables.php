<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('member');
            $table->string('status')->default('pending');
            $table->string('company')->nullable();
            $table->string('industry')->nullable();
            $table->string('title')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar_path')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('board_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token_hash', 64)->unique();
            $table->foreignId('invited_by')->nullable()->constrained('board_members')->nullOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
            $table->index(['email', 'expires_at']);
        });

        Schema::create('board_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('type')->default('group');
            $table->foreignId('created_by')->constrained('board_members')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('board_conversation_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('board_conversations')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('board_members')->cascadeOnDelete();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
            $table->unique(['conversation_id', 'member_id']);
        });

        Schema::create('board_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('board_conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('board_members')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
            $table->index(['conversation_id', 'created_at']);
        });

        Schema::create('board_push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('board_members')->cascadeOnDelete();
            $table->text('endpoint');
            $table->string('endpoint_hash', 64)->unique();
            $table->text('p256dh');
            $table->text('auth');
            $table->json('preferences')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_push_subscriptions');
        Schema::dropIfExists('board_messages');
        Schema::dropIfExists('board_conversation_members');
        Schema::dropIfExists('board_conversations');
        Schema::dropIfExists('board_invitations');
        Schema::dropIfExists('board_members');
    }
};
