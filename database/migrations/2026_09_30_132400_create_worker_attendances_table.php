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
        Schema::create('worker_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_unit_id')->constrained()->restrictOnDelete();
            $table->foreignId('daily_control_id')->constrained()->restrictOnDelete();
            $table->foreignId('cleaning_worker_id')->constrained()->restrictOnDelete();

            $table->time('arrival_time')->nullable();
            $table->boolean('is_present')->default(true);

            $table->timestamps();

            $table->unique(['daily_control_id', 'cleaning_worker_id'], 'attendance_unique_per_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_attendances');
    }
};
