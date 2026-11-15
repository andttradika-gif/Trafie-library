<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reading_challenges', function (Blueprint $t) {
            $t->id();
            $t->string('title', 150);
            $t->text('description')->nullable();
            $t->date('starts_on');
            $t->date('ends_on');
            $t->unsignedSmallInteger('target_books')->default(3);
            $t->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $t->unsignedSmallInteger('reward_xp')->default(50);
            $t->string('badge_name', 60)->nullable();
            $t->string('status', 20)->default('Active');
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('challenge_participants', function (Blueprint $t) {
            $t->id();
            $t->foreignId('challenge_id')->constrained('reading_challenges')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->timestamp('joined_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
            $t->unique(['challenge_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_participants');
        Schema::dropIfExists('reading_challenges');
    }
};