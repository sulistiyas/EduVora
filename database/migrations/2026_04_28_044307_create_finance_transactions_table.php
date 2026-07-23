<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->enum('type', ['income', 'expense'])->default('income');
            $table->string('category')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->date('transaction_date')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('recorded_by')->unsigned()->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};
