<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content')->nullable();
            $table->bigInteger('posted_by')->unsigned()->nullable()->constrained('users')->onDelete('set null');
            $table->enum('target', ['all', 'students', 'teachers', 'parents'])->default('all');
            $table->date('publish_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
