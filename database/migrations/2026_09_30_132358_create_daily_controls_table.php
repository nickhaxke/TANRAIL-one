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
        Schema::create('daily_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_unit_id')->constrained()->restrictOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->restrictOnDelete();
            $table->date('date');

            // Morning Workforce Check fields
            $table->string('workforce_check_status')->default('Pending'); // Pending -> Completed
            $table->text('zero_worker_reason')->nullable();

            $table->timestamps();

            $table->unique(['branch_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_controls');
    }
};
