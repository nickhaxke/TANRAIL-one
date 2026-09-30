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
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('business_unit_id')->constrained('business_units');
            $table->string('payment_type');
            $table->unsignedBigInteger('payment_id');
            $table->string('allocatable_type');
            $table->unsignedBigInteger('allocatable_id');
            $table->decimal('amount', 15, 4)->default(0);
            $table->date('allocation_date')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['payment_type', 'payment_id']);
            $table->index(['allocatable_type', 'allocatable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};
