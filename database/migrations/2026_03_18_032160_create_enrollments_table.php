<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->decimal('final_price', 12, 0)->default(0); // Học phí sau giảm giá
            $table->decimal('paid_amount', 12, 0)->default(0);  // Đã đóng
            $table->string('discount_note')->nullable();
            $table->enum('status', ['active', 'transferred', 'dropped', 'completed', 'reserved'])
                ->default('active');
            $table->foreignId('enrolled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
