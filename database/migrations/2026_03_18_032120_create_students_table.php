<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('parents')->nullOnDelete();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->date('dob')->nullable();
            $table->string('school')->nullable();
            $table->enum('status', ['lead', 'studying', 'dropped', 'graduated', 'reserved'])
                ->default('lead');
            // CRM: Lead tracking
            $table->string('lead_source')->nullable(); // facebook, referral, walk-in
            $table->string('lead_status')->nullable(); // new, contacted, consulting, registered
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
