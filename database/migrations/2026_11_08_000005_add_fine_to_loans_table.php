<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedInteger('fine_amount')->default(0)->after('returned_at');
            $table->timestamp('fine_paid_at')->nullable()->after('fine_amount');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['fine_amount', 'fine_paid_at']);
        });
    }
};