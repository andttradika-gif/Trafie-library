<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('book_id')->constrained()->cascadeOnDelete();
            $t->foreignId('book_copy_id')->nullable()->constrained('book_copies')->nullOnDelete();
            $t->string('status', 20)->default('Waiting');
            $t->timestamp('ready_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
            $t->index(['book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};