<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fine_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('amount');
            $t->string('method', 20);
            $t->string('status', 20)->default('Pending');
            $t->string('proof_path')->nullable();
            $t->string('reference', 100)->nullable();
            $t->string('note', 255)->nullable();
            $t->string('reject_reason', 255)->nullable();
            $t->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('verified_at')->nullable();
            $t->timestamps();
            $t->index(['loan_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fine_payments');
    }
};