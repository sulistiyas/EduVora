<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_card_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('report_card_id')->unsigned()->constrained('report_cards')->onDelete('cascade');
            $table->bigInteger('subject_id')->unsigned()->constrained('subjects')->onDelete('cascade');
            $table->decimal('score_harian', 8, 2)->nullable();
            $table->decimal('score_uts', 8, 2)->nullable();
            $table->decimal('score_uas', 8, 2)->nullable();
            $table->decimal('final_score', 8, 2)->nullable();
            $table->string('grade_letter')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_card_details');
    }
};
