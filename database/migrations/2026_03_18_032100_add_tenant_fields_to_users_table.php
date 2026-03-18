<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')
                    ->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('tenant_id')
                    ->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', [
                    'super_admin', 'center_manager', 'branch_manager',
                    'operations', 'accountant', 'teacher', 'tutor', 'parent', 'student',
                ])->default('student')->after('branch_id');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['tenant_id', 'branch_id', 'role', 'phone', 'is_active']);
        });
    }
};
