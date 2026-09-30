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
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->foreignId('purchase_order_id')->nullable()->change();
            $table->foreignId('supplier_invoice_id')->nullable()->after('purchase_order_id')->constrained('supplier_invoices')->nullOnDelete();
            $table->decimal('unallocated_amount', 15, 4)->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_payments', function (Blueprint $table) {
            $table->dropForeign(['supplier_invoice_id']);
            $table->dropColumn(['supplier_invoice_id', 'unallocated_amount']);
            $table->foreignId('purchase_order_id')->nullable(false)->change();
        });
    }
};
