<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialSettings extends Model
{
    use HasFactory;

    protected $table = 'financial_settings';

    protected $fillable = [
        'organization_id',
        'business_unit_id',
        'default_cash_account_id',
        'default_bank_account_id',
        'default_mobile_money_account_id',
        'default_card_account_id',
        'default_accounts_receivable_account_id',
        'default_accounts_payable_account_id',
        'default_sales_revenue_account_id',
        'default_tax_liability_account_id',
        'default_expense_account_id',
        'default_input_tax_recoverable_account_id',
        'default_sales_returns_account_id',
        'default_raw_materials_account_id',
        'default_finished_goods_account_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);

        static::saving(function (FinancialSettings $settings) {
            // Validate Business Unit belongs to the same Organization
            if ($settings->business_unit_id !== null) {
                $bu = BusinessUnit::withoutGlobalScopes()->find($settings->business_unit_id);
                if ($bu && $bu->organization_id !== $settings->organization_id) {
                    throw new CrossOrganizationException("Business Unit {$settings->business_unit_id} does not belong to Organization {$settings->organization_id}.");
                }
            }

            // Validate all configured accounts belong to the same Organization
            $accountFields = [
                'default_cash_account_id',
                'default_bank_account_id',
                'default_mobile_money_account_id',
                'default_card_account_id',
                'default_accounts_receivable_account_id',
                'default_accounts_payable_account_id',
                'default_sales_revenue_account_id',
                'default_tax_liability_account_id',
                'default_expense_account_id',
                'default_input_tax_recoverable_account_id',
                'default_sales_returns_account_id',
                'default_raw_materials_account_id',
                'default_finished_goods_account_id',
            ];

            foreach ($accountFields as $field) {
                if ($settings->$field !== null) {
                    $account = Account::withoutGlobalScopes()->find($settings->$field);
                    if ($account && $account->organization_id !== $settings->organization_id) {
                        throw new CrossOrganizationException("Account configured in {$field} does not belong to Organization {$settings->organization_id}.");
                    }
                }
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_cash_account_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_bank_account_id');
    }

    public function mobileMoneyAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_mobile_money_account_id');
    }

    public function cardAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_card_account_id');
    }

    public function accountsReceivableAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_accounts_receivable_account_id');
    }

    public function accountsPayableAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_accounts_payable_account_id');
    }

    public function salesRevenueAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_sales_revenue_account_id');
    }

    public function taxLiabilityAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_tax_liability_account_id');
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_expense_account_id');
    }

    public function inputTaxRecoverableAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_input_tax_recoverable_account_id');
    }

    public function salesReturnsAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_sales_returns_account_id');
    }

    public function rawMaterialsAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_raw_materials_account_id');
    }

    public function finishedGoodsAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'default_finished_goods_account_id');
    }
}
