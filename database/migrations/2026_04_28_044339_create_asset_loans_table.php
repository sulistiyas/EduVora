<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_loans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('asset_id')->unsigned()->constrained('assets')->onDelete('cascade');
            $table->bigInteger('teacher_id')->unsigned()->nullable()->constrained('teachers')->onDelete('set null');
            $table->date('loan_date')->nullable();
            $table->date('return_date')->nullable();
            $table->integer('quantity')->default(1);
            $table->text('purpose')->nullable();
            $table->enum('status', ['borrowed', 'returned'])->default('borrowed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_loans');
    }
};