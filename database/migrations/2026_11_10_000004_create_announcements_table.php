<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $t) {
            $t->id();
            $t->string('title', 150);
            $t->text('body');
            $t->string('type', 20)->default('info');
            $t->boolean('is_pinned')->default(false);
            $t->boolean('is_active')->default(true);
            $t->timestamp('published_at')->nullable();
            $t->date('ends_on')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};