<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()
                  ->comment('Giáo viên / Trợ giảng');

            $table->string('month')->comment('YYYY-MM, e.g. 2026-03');

            // Session-based calculations
            $table->integer('total_sessions')->default(0)->comment('Số buổi đã dạy');
            $table->integer('total_hours')->default(0)->comment('Số giờ');

            // Pay rates
            $table->decimal('rate_per_session', 12, 0)->default(0)->comment('Lương/buổi');
            $table->decimal('rate_per_hour', 12, 0)->default(0)->comment('Lương/giờ');

            // Computed
            $table->decimal('base_salary', 12, 0)->default(0)->comment('Lương cứng (nếu có)');
            $table->decimal('session_pay', 12, 0)->default(0)->comment('Tiền theo buổi/giờ');
            $table->decimal('bonus', 12, 0)->default(0);
            $table->decimal('deduction', 12, 0)->default(0)->comment('Khấu trừ');
            $table->decimal('total', 12, 0)->default(0)->comment('Tổng lương');

            $table->enum('status', ['draft', 'confirmed', 'paid'])->default('draft');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
            $table->unique(['user_id', 'month', 'tenant_id'], 'unique_user_month_tenant');
            $table->index(['tenant_id', 'month', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
