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
        Schema::table('worker_attendances', function (Blueprint $table) {
            $table->string('status')->default('Expected')->after('is_present');
            $table->dateTime('check_in_at')->nullable()->after('status');
            $table->dateTime('check_out_at')->nullable()->after('check_in_at');
            $table->string('absence_reason')->nullable()->after('check_out_at');
            $table->text('notes')->nullable()->after('absence_reason');
            $table->foreignId('checked_in_by_id')->nullable()->constrained('users')->nullOnDelete()->after('notes');
            $table->foreignId('checked_out_by_id')->nullable()->constrained('users')->nullOnDelete()->after('checked_in_by_id');
        });

        // Backfill data
        DB::table('worker_attendances')->where('is_present', true)->update([
            'status' => 'Present',
            'check_in_at' => DB::raw("CONCAT((SELECT date FROM daily_controls WHERE daily_controls.id = worker_attendances.daily_control_id), ' ', COALESCE(arrival_time, '00:00:00'))"),
        ]);

        DB::table('worker_attendances')->where('is_present', false)->update([
            'status' => 'Absent',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worker_attendances', function (Blueprint $table) {
            $table->dropForeign(['checked_in_by_id']);
            $table->dropForeign(['checked_out_by_id']);
            $table->dropColumn([
                'status',
                'check_in_at',
                'check_out_at',
                'absence_reason',
                'notes',
                'checked_in_by_id',
                'checked_out_by_id',
            ]);
        });
    }
};
