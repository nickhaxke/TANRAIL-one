<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('business_unit_id')->constrained('business_units')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->date('expense_date');
            $table->string('category', 100);
            $table->decimal('amount', 12, 2);
            $table->string('paid_to', 255)->nullable();
            $table->string('payment_method', 50)->default('cash');
            $table->string('receipt_number', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 50)->default('approved');
            $table->timestamps();

            $table->index(['branch_id', 'expense_date']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_expenses');
    }
};
