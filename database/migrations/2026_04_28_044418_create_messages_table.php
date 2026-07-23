<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sender_id')->unsigned()->nullable()->constrained('users')->onDelete('set null');
            $table->bigInteger('receiver_id')->unsigned()->nullable()->constrained('users')->onDelete('set null');
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->enum('type', ['inbox', 'sent'])->default('inbox');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
