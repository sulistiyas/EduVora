<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('student_id')->unsigned()->constrained('students')->onDelete('cascade');
            $table->bigInteger('fee_setting_id')->unsigned()->nullable()->constrained('fee_settings')->onDelete('set null');
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 12, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_invoices');
    }
};
