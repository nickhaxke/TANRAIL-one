@extends('layouts.management')

@section('content')
<div>
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Financial Management</h2>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0 gap-x-3">
            <a href="{{ route('management.finance') }}" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Financial Ledger Overview</a>
            <a href="{{ route('management.reports') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Executive Reports</a>
        </div>
    </div>

    <!-- Global Filters -->
    <div class="bg-white p-4 shadow sm:rounded-lg mb-8 border border-gray-100">
        <form method="GET" action="{{ route('management.finance') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-5 items-end">
            <div>
                <label for="organization_id" class="block text-sm font-medium text-gray-700">Organization</label>
                <select id="organization_id" name="organization_id" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm shadow-sm ring-1 ring-inset ring-gray-300" onchange="this.form.submit()">
                    <option value="">All Organizations</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ $orgId == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="business_unit_id" class="block text-sm font-medium text-gray-700">Business Unit</label>
                <select id="business_unit_id" name="business_unit_id" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm shadow-sm ring-1 ring-inset ring-gray-300" onchange="this.form.submit()">
                    <option value="">All Business Units</option>
                    @foreach($businessUnits as $bu)
                        <option value="{{ $bu->id }}" {{ $buId == $bu->id ? 'selected' : '' }}>{{ $bu->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm shadow-sm ring-1 ring-inset ring-gray-300" onchange="this.form.submit()">
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm shadow-sm ring-1 ring-inset ring-gray-300" onchange="this.form.submit()">
            </div>
            
            <div>
                <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Income Statement KPIs -->
    <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Income Statement Overview (Period)</h3>
    <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100 text-center">
            <dt class="truncate text-sm font-medium text-gray-500">Total Revenue</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-emerald-600">TZS {{ number_format($totalRevenue, 2) }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100 text-center">
            <dt class="truncate text-sm font-medium text-gray-500">Total Expenses</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-rose-600">TZS {{ number_format($totalExpenses, 2) }}</dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6 border border-gray-100 text-center {{ $netIncome >= 0 ? 'bg-emerald-50' : 'bg-rose-50' }}">
            <dt class="truncate text-sm font-medium text-gray-700">Net Income</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight {{ $netIncome >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">TZS {{ number_format($netIncome, 2) }}</dd>
        </div>
    </dl>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Receivables & Payables Summary -->
        <div class="bg-white shadow sm:rounded-lg border border-gray-100 p-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">AR & AP Position</h3>
            
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between text-sm font-medium mb-1">
                        <span class="text-gray-600">Accounts Receivable (AR)</span>
                        <span class="text-gray-900">TZS {{ number_format(($arBalances->total_invoiced ?? 0) - ($arBalances->total_paid ?? 0), 2) }} Outstanding</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        @php 
                            $arPerc = ($arBalances->total_invoiced ?? 0) > 0 ? (($arBalances->total_paid ?? 0) / $arBalances->total_invoiced) * 100 : 0;
                        @endphp
                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $arPerc }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Total Invoiced: TZS {{ number_format($arBalances->total_invoiced ?? 0, 2) }} | Paid: {{ number_format($arPerc, 1) }}%</p>
                </div>

                <div>
                    <div class="flex justify-between text-sm font-medium mb-1">
                        <span class="text-gray-600">Accounts Payable (AP)</span>
                        <span class="text-gray-900">TZS {{ number_format(($apBalances->total_invoiced ?? 0) - ($apBalances->total_paid ?? 0), 2) }} Outstanding</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        @php 
                            $apPerc = ($apBalances->total_invoiced ?? 0) > 0 ? (($apBalances->total_paid ?? 0) / $apBalances->total_invoiced) * 100 : 0;
                        @endphp
                        <div class="bg-rose-500 h-2.5 rounded-full" style="width: {{ $apPerc }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Total Billed: TZS {{ number_format($apBalances->total_invoiced ?? 0, 2) }} | Paid: {{ number_format($apPerc, 1) }}%</p>
                </div>
            </div>
        </div>

        <!-- Top Revenue Accounts -->
        <div class="bg-white shadow sm:rounded-lg border border-gray-100">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Top Revenue Accounts (Period)</h3>
            </div>
            <ul role="list" class="divide-y divide-gray-100">
                @forelse($topRevenueAccounts as $account)
                <li class="flex items-center justify-between py-4 px-4 sm:px-6 hover:bg-gray-50">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold leading-6 text-gray-900 truncate">{{ $account->name }}</p>
                        <p class="text-xs text-gray-500">{{ $account->code }}</p>
                    </div>
                    <div class="flex flex-none items-center gap-x-4">
                        <p class="text-sm font-semibold text-emerald-600">TZS {{ number_format($account->balance, 2) }}</p>
                    </div>
                </li>
                @empty
                <li class="py-4 px-6 text-sm text-gray-500 text-center">No revenue recorded in this period.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
