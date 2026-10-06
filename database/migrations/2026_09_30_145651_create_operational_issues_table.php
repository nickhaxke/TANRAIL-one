<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daily_control_id')->constrained('daily_controls')->cascadeOnDelete();

            $table->string('issue_type'); // e.g. Equipment Failure, Supply Shortage, Staffing, Safety Hazard
            $table->text('description');
            $table->string('status')->default('Open'); // Open, Resolved

            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_issues');
    }
};
