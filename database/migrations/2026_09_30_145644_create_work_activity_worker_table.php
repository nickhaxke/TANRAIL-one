<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_activity_worker', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_activity_id')->constrained('work_activities')->cascadeOnDelete();
            $table->foreignId('worker_attendance_id')->constrained('worker_attendances')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_activity_worker');
    }
};
