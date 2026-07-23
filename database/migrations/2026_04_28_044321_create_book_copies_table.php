<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_copies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('book_id')->unsigned()->constrained('books')->onDelete('cascade');
            $table->string('copy_number')->unique();
            $table->enum('condition', ['good', 'damaged', 'lost'])->default('good');
            $table->enum('status', ['available', 'borrowed', 'reserved'])->default('available');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_copies');
    }
};
