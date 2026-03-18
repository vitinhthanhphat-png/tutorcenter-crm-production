<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete()
                  ->comment('Manager who created the request');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()
                  ->comment('Staff member to be dispatched');
            $table->foreignId('target_tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('target_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('role_override')->nullable();
            $table->text('note')->nullable()->comment('Manager\'s reason/note');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()
                  ->comment('Super Admin who approved/rejected');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable()->comment('Admin\'s review comment');
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('user_id');
            $table->index('requester_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_requests');
    }
};
