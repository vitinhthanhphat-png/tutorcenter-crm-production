<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->date('reserved_at')->nullable()->after('notes');
            $table->date('reservation_ends_at')->nullable()->after('reserved_at');
            $table->text('reservation_note')->nullable()->after('reservation_ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['reserved_at', 'reservation_ends_at', 'reservation_note']);
        });
    }
};
