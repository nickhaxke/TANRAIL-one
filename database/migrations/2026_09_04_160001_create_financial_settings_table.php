<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('business_unit_id')->nullable()->constrained('business_units')->cascadeOnDelete();
            $table->foreignId('default_cash_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_bank_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_mobile_money_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_card_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_accounts_receivable_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_accounts_payable_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_sales_revenue_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('default_tax_liability_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->timestamps();

            $table->unique(['organization_id', 'business_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_settings');
    }
};
