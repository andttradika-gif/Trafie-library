<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reading_progress', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('book_id')->constrained()->cascadeOnDelete();
            $t->string('status', 20)->default('Reading');
            $t->unsignedInteger('current_page')->default(0);
            $t->unsignedInteger('total_pages')->nullable();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('last_activity_at')->nullable();
            $t->timestamp('completion_requested_at')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'book_id']);
        });

        Schema::create('reading_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('book_id')->constrained()->cascadeOnDelete();
            $t->date('log_date');
            $t->unsignedInteger('pages_read')->default(0);
            $t->timestamps();
            $t->unique(['user_id', 'book_id', 'log_date']);
        });

        Schema::create('reading_completions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('book_id')->constrained()->cascadeOnDelete();
            $t->foreignId('loan_id')->nullable()->constrained('loans')->nullOnDelete();
            $t->unsignedTinyInteger('rating');
            $t->text('reflection');
            $t->timestamp('completed_at');
            $t->string('status', 20)->default('Verified');
            $t->timestamp('revoked_at')->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'book_id']);
        });

        Schema::create('xp_transactions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->integer('amount');
            $t->string('reason', 40);
            $t->string('ref', 60);
            $t->string('note', 255)->nullable();
            $t->timestamps();
            $t->unique(['user_id', 'reason', 'ref']);
        });

        Schema::create('user_achievements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('code', 40);
            $t->timestamp('unlocked_at');
            $t->timestamps();
            $t->unique(['user_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('xp_transactions');
        Schema::dropIfExists('reading_completions');
        Schema::dropIfExists('reading_logs');
        Schema::dropIfExists('reading_progress');
    }
};