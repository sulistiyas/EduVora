<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id('room_id');
            $table->string('room_name')->unique();
            $table->string('code')->unique();
            $table->enum('type',['classroom','lab','library','office','sport']);
            $table->tinyInteger('floor')->unsigned();
            $table->string('building');
            $table->tinyInteger('capacity')->unsigned();
            $table->string('facility');
            $table->tinyInteger('is_available')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};