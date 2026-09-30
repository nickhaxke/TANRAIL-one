<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\Enums\PaymentMethod;
use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Exceptions\FinancialConfigurationException;
use App\Domains\Core\Models\Account;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\FinancialSettings;
use App\Domains\Core\Models\TaxCategory;

class AccountResolver
{
    /**
     * Resolve a required account for a Business Unit with deterministic fallback to Organization.
     *
     * @throws FinancialConfigurationException
     * @throws CrossOrganizationException
     */
    public function resolve(BusinessUnit $businessUnit, string $accountKey): Account
    {
        $accountId = null;

        // 1. Try Business Unit specific setting
        $buSetting = FinancialSettings::withoutGlobalScopes()
            ->where('organization_id', $businessUnit->organization_id)
            ->where('business_unit_id', $businessUnit->id)
            ->first();

        if ($buSetting && $buSetting->$accountKey !== null) {
            $accountId = $buSetting->$accountKey;
        } else {
            // 2. Deterministic fallback to Organization default setting
            $orgSetting = FinancialSettings::withoutGlobalScopes()
                ->where('organization_id', $businessUnit->organization_id)
                ->whereNull('business_unit_id')
                ->first();

            if ($orgSetting && $orgSetting->$accountKey !== null) {
                $accountId = $orgSetting->$accountKey;
            }
        }

        if ($accountId === null) {
            throw new FinancialConfigurationException(
                "Required financial account '{$accountKey}' is not configured for Business Unit '{$businessUnit->name}' (ID: {$businessUnit->id}) or Organization (ID: {$businessUnit->organization_id})."
            );
        }

        $account = Account::withoutGlobalScopes()->find($accountId);

        if (! $account) {
            throw new FinancialConfigurationException(
                "Configured account ID {$accountId} for '{$accountKey}' does not exist."
            );
        }

        if (! $account->is_active) {
            throw new FinancialConfigurationException(
                "Configured account '{$account->name}' (ID: {$account->id}) for '{$accountKey}' is inactive."
            );
        }

        if ($account->organization_id !== $businessUnit->organization_id) {
            throw new CrossOrganizationException(
                "Configured account '{$account->name}' belongs to Organization {$account->organization_id}, expected {$businessUnit->organization_id}."
            );
        }

        return $account;
    }

    /**
     * Resolve asset account according to payment method.
     *
     * @throws FinancialConfigurationException
     * @throws CrossOrganizationException
     */
    public function resolvePaymentAccount(BusinessUnit $businessUnit, PaymentMethod|string $method): Account
    {
        $methodValue = $method instanceof PaymentMethod ? $method->value : $method;

        $key = match ($methodValue) {
            PaymentMethod::CASH->value => 'default_cash_account_id',
            PaymentMethod::BANK_TRANSFER->value => 'default_bank_account_id',
            PaymentMethod::MOBILE_MONEY->value => 'default_mobile_money_account_id',
            PaymentMethod::CARD->value => 'default_card_account_id',
            default => throw new FinancialConfigurationException("Unsupported or unmapped payment method: '{$methodValue}'."),
        };

        return $this->resolve($businessUnit, $key);
    }

    /**
     * Resolve tax liability account for a TaxCategory within the context of a Business Unit.
     *
     * @throws FinancialConfigurationException
     * @throws CrossOrganizationException
     */
    public function resolveTaxLiabilityAccount(TaxCategory $taxCategory, BusinessUnit $businessUnit): Account
    {
        if ($taxCategory->liability_account_id !== null) {
            $account = Account::withoutGlobalScopes()->find($taxCategory->liability_account_id);

            if (! $account) {
                throw new FinancialConfigurationException(
                    "Tax liability account ID {$taxCategory->liability_account_id} for Tax Category '{$taxCategory->code}' does not exist."
                );
            }

            if (! $account->is_active) {
                throw new FinancialConfigurationException(
                    "Tax liability account '{$account->name}' (ID: {$account->id}) for Tax Category '{$taxCategory->code}' is inactive."
                );
            }

            if ($account->organization_id !== $businessUnit->organization_id) {
                throw new CrossOrganizationException(
                    "Tax liability account '{$account->name}' belongs to Organization {$account->organization_id}, expected {$businessUnit->organization_id}."
                );
            }

            return $account;
        }

        // Fall back to Organization/BU default tax liability account if TaxCategory does not override it
        return $this->resolve($businessUnit, 'default_tax_liability_account_id');
    }

    /**
     * Resolve expense / inventory asset account for a Business Unit.
     *
     * @throws FinancialConfigurationException
     * @throws CrossOrganizationException
     */
    public function resolveExpenseAccount(BusinessUnit $businessUnit): Account
    {
        return $this->resolve($businessUnit, 'default_expense_account_id');
    }

    /**
     * Resolve recoverable input tax account for a Business Unit.
     *
     * @throws FinancialConfigurationException
     * @throws CrossOrganizationException
     */
    public function resolveInputTaxRecoverableAccount(BusinessUnit $businessUnit): Account
    {
        return $this->resolve($businessUnit, 'default_input_tax_recoverable_account_id');
    }

    /**
     * Resolve sales returns account for a Business Unit. Falls back to sales revenue account if sales returns account is not configured.
     *
     * @throws FinancialConfigurationException
     * @throws CrossOrganizationException
     */
    public function resolveSalesReturnsAccount(BusinessUnit $businessUnit): Account
    {
        try {
            return $this->resolve($businessUnit, 'default_sales_returns_account_id');
        } catch (FinancialConfigurationException $e) {
            // Safe fallback to Sales Revenue account if explicit Sales Returns account is not set
            return $this->resolve($businessUnit, 'default_sales_revenue_account_id');
        }
    }
}
