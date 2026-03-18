<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Transfer tracking (status column already exists in enrollments table)
            $table->foreignId('transferred_from_id')->nullable()
                  ->constrained('enrollments')->nullOnDelete()
                  ->comment('Original enrollment before transfer');
            $table->foreignId('transferred_to_id')->nullable()
                  ->constrained('enrollments')->nullOnDelete()
                  ->comment('New enrollment after transfer');
            $table->timestamp('transferred_at')->nullable();
            $table->decimal('credit_balance', 12, 0)->default(0)
                  ->comment('Credit carry-over from partial payments');
            $table->text('transfer_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['transferred_from_id']);
            $table->dropForeign(['transferred_to_id']);
            $table->dropColumn([
                'transferred_from_id', 'transferred_to_id',
                'transferred_at', 'credit_balance', 'transfer_note',
            ]);
        });
    }
};
