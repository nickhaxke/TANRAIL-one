@extends('layouts.management')

@section('content')
<div>
    <!-- Corporate Header -->
    <div class="bg-white border-b-2 border-gray-900 pb-5 mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="text-gray-500 font-bold text-[10px] uppercase tracking-widest mb-1">Corporate Procurement Hub</div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight uppercase">Procurement Management</h1>
            <p class="text-gray-500 text-xs font-semibold mt-1 uppercase tracking-wide">Enterprise expenditure, purchase orders, and supplier commitments.</p>
        </div>
        <div class="flex gap-x-3">
            <a href="{{ route('management.approvals') }}" class="inline-flex items-center bg-white px-4 py-2 text-xs font-bold text-gray-700 border border-gray-300 hover:bg-gray-50 uppercase tracking-wider transition-colors">
                Approvals Queue
            </a>
            <a href="{{ route('management.procurement') }}" class="inline-flex items-center bg-gray-900 px-4 py-2 text-xs font-bold text-white border border-gray-900 hover:bg-gray-800 uppercase tracking-wider transition-colors">
                All Purchase Orders
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 p-4 mb-6 border border-emerald-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Global Filters -->
    <div class="bg-white border border-gray-300 p-4 mb-6 flex flex-col md:flex-row items-end gap-4 shadow-sm">
        <form method="GET" action="{{ route('management.procurement') }}" class="w-full flex flex-col md:flex-row items-end gap-4">
            <div class="flex-1 w-full">
                <label for="organization_id" class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Organization</label>
                <select id="organization_id" name="organization_id" class="block w-full rounded border-gray-300 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                    <option value="">All Organizations</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ $orgId == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex-1 w-full">
                <label for="business_unit_id" class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Business Unit</label>
                <select id="business_unit_id" name="business_unit_id" class="block w-full rounded border-gray-300 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
                    <option value="">All Business Units</option>
                    @foreach($businessUnits as $bu)
                        <option value="{{ $bu->id }}" {{ $buId == $bu->id ? 'selected' : '' }}>{{ $bu->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 w-full">
                <label for="start_date" class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="block w-full rounded border-gray-300 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
            </div>

            <div class="flex-1 w-full">
                <label for="end_date" class="block text-[11px] font-bold uppercase text-gray-500 mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="block w-full rounded border-gray-300 py-1.5 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm" onchange="this.form.submit()">
            </div>
            
            <div>
                <button type="submit" class="inline-flex justify-center items-center rounded bg-gray-100 px-4 py-1.5 text-sm font-semibold text-gray-800 border border-gray-300 hover:bg-gray-200 transition-colors shadow-sm">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Procurement KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white border border-gray-300 p-5 shadow-sm">
            <div class="text-[11px] font-bold uppercase text-gray-500 tracking-wider">PO Commitments</div>
            <div class="mt-2 text-2xl font-black text-gray-900">TZS {{ number_format($totalCommitted, 2) }}</div>
            <div class="text-xs text-gray-500 mt-1">Approved & partial commitments</div>
        </div>
        <div class="bg-white border border-gray-300 p-5 shadow-sm">
            <div class="text-[11px] font-bold uppercase text-gray-500 tracking-wider">Total Supplier Billed</div>
            <div class="mt-2 text-2xl font-black text-gray-900">TZS {{ number_format($totalBilled, 2) }}</div>
            <div class="text-xs text-gray-500 mt-1">Invoiced by vendors</div>
        </div>
        <div class="bg-white border border-gray-300 p-5 shadow-sm border-b-4 border-b-red-600">
            <div class="text-[11px] font-bold uppercase text-gray-500 tracking-wider">Outstanding Exposure</div>
            <div class="mt-2 text-2xl font-black text-red-600">TZS {{ number_format($outstandingExposure, 2) }}</div>
            <div class="text-xs text-red-400 mt-1">Unpaid approved bills</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Recent POs -->
        <div class="bg-white border border-gray-300 shadow-sm lg:col-span-2">
            <div class="px-5 py-4 border-b border-gray-300 bg-gray-50 flex justify-between items-center">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-800">Recent Purchase Orders</h3>
            </div>
            
            @if($recentPOs->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">PO Number</th>
                            <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Supplier & Unit</th>
                            <th scope="col" class="px-5 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-5 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentPOs as $po)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ $po->po_number ?? 'PO-'.$po->id }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $po->supplier->name ?? 'Unknown Supplier' }}</div>
                                <div class="text-xs text-gray-500">{{ $po->businessUnit->name ?? 'Unknown BU' }}</div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                TZS {{ number_format($po->total, 2) }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-right">
                                <span class="inline-flex items-center rounded bg-gray-100 px-2 py-0.5 text-[11px] font-bold text-gray-800 border border-gray-300 uppercase">
                                    {{ $po->status->value ?? $po->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-8 text-center text-gray-500 text-sm">
                No recent purchase orders found in this period.
            </div>
            @endif
        </div>

        <!-- Top Suppliers -->
        <div class="bg-white border border-gray-300 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-300 bg-gray-50">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-800">Top Suppliers (by Spend)</h3>
            </div>
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($topSuppliers as $supplier)
                <li class="px-5 py-4 hover:bg-gray-50">
                    <div class="text-sm font-bold text-gray-900">{{ $supplier->name }}</div>
                    <div class="text-xs font-semibold text-gray-600 mt-1">TZS {{ number_format($supplier->total_billed, 2) }}</div>
                </li>
                @empty
                <li class="px-5 py-8 text-center text-sm text-gray-500">
                    No billing activity in this period.
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
