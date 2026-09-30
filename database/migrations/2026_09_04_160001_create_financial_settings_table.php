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
            $table->unsignedBigInteger('default_cash_account_id')->nullable();
            $table->unsignedBigInteger('default_bank_account_id')->nullable();
            $table->unsignedBigInteger('default_mobile_money_account_id')->nullable();
            $table->unsignedBigInteger('default_card_account_id')->nullable();
            $table->unsignedBigInteger('default_accounts_receivable_account_id')->nullable();
            $table->unsignedBigInteger('default_accounts_payable_account_id')->nullable();
            $table->unsignedBigInteger('default_sales_revenue_account_id')->nullable();
            $table->unsignedBigInteger('default_tax_liability_account_id')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'business_unit_id']);

            // Short FK names to stay within MySQL's 64-char identifier limit
            $table->foreign('default_cash_account_id', 'fs_cash_fk')->references('id')->on('accounts')->nullOnDelete();
            $table->foreign('default_bank_account_id', 'fs_bank_fk')->references('id')->on('accounts')->nullOnDelete();
            $table->foreign('default_mobile_money_account_id', 'fs_mobile_fk')->references('id')->on('accounts')->nullOnDelete();
            $table->foreign('default_card_account_id', 'fs_card_fk')->references('id')->on('accounts')->nullOnDelete();
            $table->foreign('default_accounts_receivable_account_id', 'fs_ar_fk')->references('id')->on('accounts')->nullOnDelete();
            $table->foreign('default_accounts_payable_account_id', 'fs_ap_fk')->references('id')->on('accounts')->nullOnDelete();
            $table->foreign('default_sales_revenue_account_id', 'fs_sales_fk')->references('id')->on('accounts')->nullOnDelete();
            $table->foreign('default_tax_liability_account_id', 'fs_tax_fk')->references('id')->on('accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_settings');
    }
};
