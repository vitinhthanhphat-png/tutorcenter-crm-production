<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('class_sessions')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['present', 'absent_with_leave', 'absent_no_leave', 'late'])
                ->default('present');
            $table->decimal('grade', 4, 1)->nullable(); // Điểm bài tập, 0-10
            $table->text('teacher_comment')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'student_id']); // Mỗi học sinh 1 bản ghi / buổi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
