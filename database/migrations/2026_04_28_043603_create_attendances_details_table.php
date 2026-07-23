<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_details', function (Blueprint $table) {

            $table->id('attendance_detail_id');

            /**
             * PARENT SESSION
             */
            $table->foreignId('attendance_session_id')
                ->constrained('attendance_sessions', 'attendance_session_id')
                ->cascadeOnDelete();

            /**
             * STUDENT
             */
            $table->foreignId('student_id')
                ->constrained('students', 'id')
                ->cascadeOnDelete();

            /**
             * ATTENDANCE STATUS
             *
             * H = Hadir
             * I = Izin
             * S = Sakit
             * A = Alpha
             * L = Late/Terlambat
             */
            $table->enum('status', [
                'H',
                'I',
                'S',
                'A',
                'L',
            ])->default('H');

            /**
             * OPTIONAL NOTES
             */
            $table->text('note')
                ->nullable();

            /**
             * ATTACHMENT
             *
             * Surat izin / sakit
             */
            $table->string('attachment')
                ->nullable();

            /**
             * PARENT NOTIFICATION
             */
            $table->timestamp('notified_at')
                ->nullable();

            $table->timestamps();

            /**
             * PREVENT DUPLICATE STUDENT
             * INSIDE SAME SESSION
             */
            $table->unique([
                'attendance_session_id',
                'student_id',
            ], 'unique_student_attendance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_details');
    }
};
