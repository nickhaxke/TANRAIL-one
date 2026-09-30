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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('business_unit_id')->constrained('business_units');
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('invoice_number');
            $table->date('issue_date')->index();
            $table->date('due_date')->index();
            $table->string('currency', 3)->default('TZS');
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->decimal('amount_paid', 15, 4)->default(0);
            $table->decimal('balance_due', 15, 4)->default(0);
            $table->string('status')->default('draft')->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['business_unit_id', 'invoice_number']);
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->foreignId('tax_category_id')->nullable()->constrained('tax_categories');
            $table->string('item_name_snapshot');
            $table->string('description')->nullable();
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('tax_rate_snapshot', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
    }
};
