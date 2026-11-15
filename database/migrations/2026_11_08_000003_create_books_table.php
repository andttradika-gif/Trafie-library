<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn', 20)->nullable()->unique();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('cover')->nullable();
            $table->string('author');
            $table->string('publisher');
            $table->unsignedSmallInteger('publication_year');
            $table->unsignedInteger('pages')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('language', 50)->default('Indonesia');
            $table->string('shelf_location', 50)->nullable();
            $table->timestamps();
            $table->index('title');
            $table->index('author');
        });

        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->string('copy_code')->unique();
            $table->string('condition', 30)->default('Bagus');
            $table->date('procurement_date')->nullable();
            $table->string('status', 30)->default('Available');
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
        Schema::dropIfExists('books');
    }
};