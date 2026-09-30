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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('business_unit_id')->constrained('business_units');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('reference_number')->nullable();
            $table->string('status');
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('tax_total', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders');
            $table->foreignId('item_id')->constrained('items');
            $table->decimal('quantity', 10, 4);
            $table->decimal('unit_price', 15, 4);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('subtotal', 15, 4);
            $table->decimal('total', 15, 4);
            $table->decimal('received_quantity', 10, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders');
            $table->foreignId('inventory_location_id')->constrained('inventory_locations');
            $table->string('reference_number')->nullable();
            $table->timestamp('received_at');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('purchase_receipt_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_receipt_id')->constrained('purchase_receipts');
            $table->foreignId('purchase_order_line_id')->constrained('purchase_order_lines');
            $table->foreignId('item_id')->constrained('items');
            $table->decimal('quantity', 10, 4);
            $table->timestamps();
        });

        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders');
            $table->decimal('amount', 15, 4);
            $table->string('method');
            $table->string('reference')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
        Schema::dropIfExists('purchase_receipt_lines');
        Schema::dropIfExists('purchase_receipts');
        Schema::dropIfExists('purchase_order_lines');
        Schema::dropIfExists('purchase_orders');
    }
};
