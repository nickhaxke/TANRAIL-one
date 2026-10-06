<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cleaning_workers', function (Blueprint $table) {
            $table->foreignId('current_branch_id')->nullable()->after('business_unit_id')->constrained('branches')->nullOnDelete();
            $table->foreignId('current_supervisor_id')->nullable()->after('current_branch_id')->constrained('users')->nullOnDelete();
        });

        Schema::create('cleaning_worker_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_unit_id')->constrained('business_units')->cascadeOnDelete();
            $table->foreignId('cleaning_worker_id')->constrained('cleaning_workers')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['cleaning_worker_id', 'end_date']);
            $table->index(['branch_id', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleaning_worker_assignments');

        Schema::table('cleaning_workers', function (Blueprint $table) {
            $table->dropForeign(['current_branch_id']);
            $table->dropForeign(['current_supervisor_id']);
            $table->dropColumn(['current_branch_id', 'current_supervisor_id']);
        });
    }
};
