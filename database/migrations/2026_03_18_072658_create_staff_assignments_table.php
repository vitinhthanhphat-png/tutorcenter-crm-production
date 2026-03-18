<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('role_override')->nullable()->comment('Override role for this assignment, null = use home role');
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable()->comment('null = indefinite');
            $table->timestamps();

            $table->unique(['user_id', 'tenant_id', 'branch_id'], 'unique_user_tenant_branch');
            $table->index(['tenant_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_assignments');
    }
};
