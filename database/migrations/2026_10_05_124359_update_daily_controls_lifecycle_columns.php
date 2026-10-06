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
        Schema::table('daily_controls', function (Blueprint $table) {
            $table->dropUnique(['branch_id', 'date']);

            $table->string('shift')->default('Day')->after('date');
            $table->dateTime('submitted_at')->nullable()->after('status');
            $table->foreignId('submitted_by_id')->nullable()->constrained('users')->nullOnDelete()->after('submitted_at');
            $table->dateTime('reviewed_at')->nullable()->after('submitted_by_id');
            $table->foreignId('reviewed_by_id')->nullable()->constrained('users')->nullOnDelete()->after('reviewed_at');
            $table->text('review_notes')->nullable()->after('reviewed_by_id');
            $table->text('supervisor_remarks')->nullable()->after('review_notes');
            $table->json('summary_snapshot')->nullable()->after('supervisor_remarks');

            $table->unique(['branch_id', 'supervisor_id', 'date', 'shift'], 'daily_controls_b_s_d_s_unique');
        });

        // Backfill data
        DB::table('daily_controls')->whereNull('status')->update([
            'status' => 'Open',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_controls', function (Blueprint $table) {
            $table->dropUnique('daily_controls_b_s_d_s_unique');
            $table->dropForeign(['submitted_by_id']);
            $table->dropForeign(['reviewed_by_id']);
            $table->dropColumn([
                'shift',
                'submitted_at',
                'submitted_by_id',
                'reviewed_at',
                'reviewed_by_id',
                'review_notes',
                'supervisor_remarks',
                'summary_snapshot',
            ]);
            $table->unique(['branch_id', 'date']);
        });
    }
};
