<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_settings', function (Blueprint $table) {
            $table->foreignId('default_expense_account_id')->nullable()->after('default_tax_liability_account_id')->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_input_tax_recoverable_account_id')->nullable()->after('default_expense_account_id')->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_sales_returns_account_id')->nullable()->after('default_input_tax_recoverable_account_id')->constrained('accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('financial_settings', function (Blueprint $table) {
            $table->dropForeign(['default_expense_account_id']);
            $table->dropForeign(['default_input_tax_recoverable_account_id']);
            $table->dropForeign(['default_sales_returns_account_id']);

            $table->dropColumn([
                'default_expense_account_id',
                'default_input_tax_recoverable_account_id',
                'default_sales_returns_account_id',
            ]);
        });
    }
};
