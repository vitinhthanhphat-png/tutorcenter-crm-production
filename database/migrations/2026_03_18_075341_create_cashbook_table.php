<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashbook', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('type', ['income', 'expense'])->comment('Thu/Chi');
            $table->string('category')->comment('Điện, Nước, Lương, Mua sắm, Học phí, Khác...');
            $table->string('description');
            $table->decimal('amount', 15, 0)->comment('VND');
            $table->date('transaction_date');
            $table->string('reference')->nullable()->comment('Số phiếu, số HĐ...');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->text('note')->nullable();

            $table->timestamps();
            $table->index(['tenant_id', 'branch_id', 'transaction_date']);
            $table->index(['tenant_id', 'type', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashbook');
    }
};
