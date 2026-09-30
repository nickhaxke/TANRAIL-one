<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Organization;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function system()
    {
        $settings = Cache::get('tanrail_system_settings', [
            'system_name' => 'TANRAIL ONE - Enterprise Management',
            'headquarters_city' => 'Dar es Salaam',
            'timezone' => 'Africa/Dar_es_Salaam',
            'primary_language' => 'en',
            'session_timeout' => 60,
            'maintenance_mode' => false,
        ]);

        return view('management.settings.system', compact('settings'));
    }

    public function updateSystem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'system_name' => 'required|string|max:255',
            'headquarters_city' => 'required|string|max:100',
            'timezone' => 'required|string|max:100',
            'primary_language' => 'required|string|in:en,sw',
            'session_timeout' => 'required|integer|min:15|max:480',
            'maintenance_mode' => 'nullable|boolean',
        ]);

        $validated['maintenance_mode'] = $request->boolean('maintenance_mode');

        Cache::forever('tanrail_system_settings', $validated);

        return redirect()->route('management.settings.system')->with('success', 'System preferences have been saved successfully.');
    }

    public function organization()
    {
        $organization = Organization::first();
        if (! $organization) {
            $organization = Organization::create([
                'name' => 'TANRAIL Investments Limited',
                'code' => 'TANRAIL',
                'trading_name' => 'TANRAIL',
                'status' => true,
                'country' => 'Tanzania',
                'city' => 'Dar es Salaam',
            ]);
        }

        return view('management.settings.organization', compact('organization'));
    }

    public function updateOrganization(Request $request): RedirectResponse
    {
        $organization = Organization::first();
        if (! $organization) {
            $organization = Organization::create([
                'name' => 'TANRAIL Investments Limited',
                'code' => 'TANRAIL',
                'status' => true,
            ]);
        }

        $validated = $request->validate([
            'legal_name' => 'required|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50',
            'tin_number' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
        ]);

        $validated['name'] = $validated['legal_name'];
        unset($validated['legal_name']);

        $organization->update($validated);

        return redirect()->route('management.settings.organization')->with('success', 'TANRAIL Corporate Profile has been updated successfully.');
    }

    public function financial()
    {
        $settings = Cache::get('tanrail_financial_settings', [
            'base_currency' => 'TZS',
            'secondary_currency' => 'USD',
            'fiscal_year_start' => 'July',
            'payment_terms' => 'Net 30',
            'accounting_method' => 'accrual',
            'auto_post_gl' => true,
            'decimal_places' => 2,
        ]);

        return view('management.settings.financial', compact('settings'));
    }

    public function updateFinancial(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'base_currency' => 'required|string|max:10',
            'secondary_currency' => 'nullable|string|max:10',
            'fiscal_year_start' => 'required|string|max:20',
            'payment_terms' => 'required|string|max:50',
            'accounting_method' => 'required|string|in:accrual,cash',
            'auto_post_gl' => 'nullable|boolean',
            'decimal_places' => 'required|integer|min:0|max:4',
        ]);

        $validated['auto_post_gl'] = $request->boolean('auto_post_gl');

        Cache::forever('tanrail_financial_settings', $validated);

        return redirect()->route('management.settings.financial')->with('success', 'Financial defaults and ledger settings saved successfully.');
    }

    public function numbering()
    {
        $settings = Cache::get('tanrail_numbering_settings', [
            'business_unit_prefix' => 'BU-',
            'branch_prefix' => 'BR-',
            'staff_id_prefix' => 'TRC-EMP-',
            'code_padding' => 3,
            'auto_generate_codes' => true,
        ]);

        return view('management.settings.numbering', compact('settings'));
    }

    public function updateNumbering(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_unit_prefix' => 'required|string|max:10',
            'branch_prefix' => 'required|string|max:10',
            'staff_id_prefix' => 'required|string|max:15',
            'code_padding' => 'required|integer|min:2|max:6',
            'auto_generate_codes' => 'nullable|boolean',
        ]);

        $validated['auto_generate_codes'] = $request->boolean('auto_generate_codes');

        Cache::forever('tanrail_numbering_settings', $validated);

        return redirect()->route('management.settings.numbering')->with('success', 'Administrative numbering and code formatting settings saved successfully.');
    }

    public function tax()
    {
        $settings = Cache::get('tanrail_tax_settings', [
            'vat_rate' => 18.00,
            'vat_number' => '40012345678',
            'wht_goods_rate' => 2.00,
            'wht_services_rate' => 5.00,
            'railway_development_levy' => 1.50,
            'tax_exempt_sovereign' => true,
        ]);

        return view('management.settings.tax', compact('settings'));
    }

    public function updateTax(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vat_rate' => 'required|numeric|min:0|max:100',
            'vat_number' => 'nullable|string|max:50',
            'wht_goods_rate' => 'required|numeric|min:0|max:100',
            'wht_services_rate' => 'required|numeric|min:0|max:100',
            'railway_development_levy' => 'required|numeric|min:0|max:100',
            'tax_exempt_sovereign' => 'nullable|boolean',
        ]);

        $validated['tax_exempt_sovereign'] = $request->boolean('tax_exempt_sovereign');

        Cache::forever('tanrail_tax_settings', $validated);

        return redirect()->route('management.settings.tax')->with('success', 'Tax rates and statutory levies saved successfully.');
    }

    public function notifications()
    {
        $settings = Cache::get('tanrail_notifications_settings', [
            'notify_manager_assigned' => true,
            'notify_branch_status' => true,
            'notify_audit_events' => true,
            'notification_email' => 'admin@tanrail.co.tz',
        ]);

        return view('management.settings.notifications', compact('settings'));
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'notification_email' => 'required|email|max:255',
            'notify_manager_assigned' => 'nullable|boolean',
            'notify_branch_status' => 'nullable|boolean',
            'notify_audit_events' => 'nullable|boolean',
        ]);

        $validated['notify_manager_assigned'] = $request->boolean('notify_manager_assigned');
        $validated['notify_branch_status'] = $request->boolean('notify_branch_status');
        $validated['notify_audit_events'] = $request->boolean('notify_audit_events');

        Cache::forever('tanrail_notifications_settings', $validated);

        return redirect()->route('management.settings.notifications')->with('success', 'Administrative notification settings saved successfully.');
    }
}
