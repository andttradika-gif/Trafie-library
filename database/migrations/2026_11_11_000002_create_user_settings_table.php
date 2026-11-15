<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('default_duration')->default(7);
            $t->boolean('n_due')->default(true);
            $t->boolean('n_reservation')->default(true);
            $t->boolean('n_fines')->default(true);
            $t->boolean('n_announcements')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};