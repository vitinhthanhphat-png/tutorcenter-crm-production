<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();

            // Contact info
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('parent_name')->nullable()->comment('Tên phụ huynh nếu là học sinh nhỏ');

            // Lead pipeline
            $table->enum('status', [
                'new',          // Mới vào
                'contacted',    // Đã liên hệ
                'consulting',   // Đang tư vấn
                'test_booked',  // Hẹn test đầu vào
                'registered',   // Đã đăng ký
                'lost',         // Mất lead
            ])->default('new');

            $table->string('source')->nullable()->comment('Facebook, Zalo, Giới thiệu, Website, Tờ rơi...');
            $table->string('interested_course')->nullable()->comment('Khóa học quan tâm');
            $table->text('note')->nullable();

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Nhân viên tư vấn phụ trách');
            $table->date('follow_up_at')->nullable()->comment('Ngày hẹn gọi lại');
            $table->foreignId('converted_to_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'follow_up_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
