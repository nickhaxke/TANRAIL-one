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
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('business_unit_id')->constrained('business_units');
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->string('supplier_bill_number');
            $table->string('internal_reference');
            $table->date('bill_date')->index();
            $table->date('due_date')->index();
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->decimal('amount_paid', 15, 4)->default(0);
            $table->decimal('balance_due', 15, 4)->default(0);
            $table->string('status')->default('draft')->index();
            $table->string('match_status')->default('unmatched')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['business_unit_id', 'internal_reference']);
            $table->index(['supplier_id', 'supplier_bill_number']);
        });

        Schema::create('supplier_invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_invoice_id')->constrained('supplier_invoices')->cascadeOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->foreignId('tax_category_id')->nullable()->constrained('tax_categories');
            $table->foreignId('purchase_receipt_line_id')->nullable()->constrained('purchase_receipt_lines')->nullOnDelete();
            $table->string('description');
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('tax_rate_snapshot', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->decimal('received_quantity_reference', 15, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invoice_lines');
        Schema::dropIfExists('supplier_invoices');
    }
};
