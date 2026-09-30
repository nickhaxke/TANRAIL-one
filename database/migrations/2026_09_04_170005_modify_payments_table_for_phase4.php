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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->change();
            $table->foreignId('invoice_id')->nullable()->after('order_id')->constrained('invoices')->nullOnDelete();
            $table->decimal('unallocated_amount', 15, 4)->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn(['invoice_id', 'unallocated_amount']);
            $table->foreignId('order_id')->nullable(false)->change();
        });
    }
};
