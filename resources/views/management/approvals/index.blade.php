@extends('layouts.management')

@section('content')
<div>
    <!-- Corporate Header -->
    <div class="bg-white border-b-2 border-gray-900 pb-5 mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="text-gray-500 font-bold text-[10px] uppercase tracking-widest mb-1">Corporate Procurement Hub</div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight uppercase">Approval Center</h1>
            <p class="text-gray-500 text-xs font-semibold mt-1 uppercase tracking-wide">Review and authorize pending business unit requests.</p>
        </div>
        <div class="flex gap-x-3">
            <a href="{{ route('management.approvals') }}" class="inline-flex items-center bg-gray-900 px-4 py-2 text-xs font-bold text-white border border-gray-900 hover:bg-gray-800 uppercase tracking-wider transition-colors">
                Approvals Queue
            </a>
            <a href="{{ route('management.procurement') }}" class="inline-flex items-center bg-white px-4 py-2 text-xs font-bold text-gray-700 border border-gray-300 hover:bg-gray-50 uppercase tracking-wider transition-colors">
                All Purchase Orders
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-md bg-red-50 p-4 mb-6 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Purchase Orders Awaiting Approval -->
        <div class="bg-white shadow sm:rounded-lg border border-gray-100">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Purchase Orders</h3>
                <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20">{{ count($purchaseOrders) }} Pending</span>
            </div>
            
            @if(count($purchaseOrders) > 0)
            <ul role="list" class="divide-y divide-gray-100">
                @foreach($purchaseOrders as $po)
                <li class="flex items-center justify-between gap-x-6 py-5 px-4 sm:px-6 hover:bg-gray-50">
                    <div class="min-w-0">
                        <div class="flex items-start gap-x-3">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ $po->po_number ?? 'PO-'.$po->id }}</p>
                        </div>
                        <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                            <p class="whitespace-nowrap">{{ $po->supplier->name ?? 'Unknown Supplier' }}</p>
                            <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current"><circle cx="1" cy="1" r="1" /></svg>
                            <p class="truncate">{{ $po->businessUnit->name ?? 'Unknown BU' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-none items-center gap-x-4">
                        <p class="text-sm font-semibold text-gray-900">TZS {{ number_format($po->total ?? 0, 2) }}</p>
                        <button type="button" onclick="document.getElementById('viewDetailsModal_{{ $po->id }}').classList.remove('hidden')" class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-blue-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block">View & Actions</button>

                        <!-- View Details Modal -->
                        <div id="viewDetailsModal_{{ $po->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
                            <div class="bg-white border border-gray-300 rounded-sm max-w-2xl w-full p-0 shadow-2xl relative">
                                <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                                    <div>
                                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">Purchase Request</h3>
                                        <p class="text-xs font-bold text-gray-500 uppercase mt-1">PO Number: {{ $po->reference_number }}</p>
                                    </div>
                                    <button type="button" onclick="document.getElementById('viewDetailsModal_{{ $po->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
                                </div>
                                
                                <div class="p-6">
                                    <div class="mb-4 flex justify-between">
                                        <div>
                                            <p class="text-xs font-bold text-gray-500 uppercase">Supplier</p>
                                            <p class="text-sm font-bold text-gray-900">{{ $po->supplier->name ?? 'Unknown Supplier' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-bold text-gray-500 uppercase">Branch / BU</p>
                                            <p class="text-sm font-bold text-gray-900">{{ $po->businessUnit->name ?? 'Unknown BU' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="border border-gray-200 rounded-sm overflow-hidden mb-6">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-gray-50 border-b border-gray-200">
                                                <tr>
                                                    <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider">Item</th>
                                                    <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right">Qty</th>
                                                    <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right">Unit Price</th>
                                                    <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @forelse($po->lines as $line)
                                                <tr>
                                                    <td class="py-2 px-3 font-bold text-gray-900">{{ $line->item->name ?? 'Unknown' }}</td>
                                                    <td class="py-2 px-3 text-right font-mono">{{ number_format($line->quantity, 1) }}</td>
                                                    <td class="py-2 px-3 text-right font-mono">{{ number_format($line->unit_price, 2) }}</td>
                                                    <td class="py-2 px-3 text-right font-mono text-gray-900 font-bold">{{ number_format($line->total, 2) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="py-4 text-center text-gray-500">No items on this request.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot class="bg-gray-50 border-t border-gray-200">
                                                <tr>
                                                    <td colspan="3" class="py-2 px-3 text-right font-bold text-gray-900 uppercase tracking-widest text-[10px]">Grand Total</td>
                                                    <td class="py-2 px-3 text-right font-mono font-black text-gray-900">{{ number_format($po->total, 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="flex justify-between gap-3 pt-4 border-t border-gray-200">
                                        <form method="POST" action="{{ route('management.approvals.po.reject', $po->id) }}">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 border border-red-300 bg-red-50 text-xs font-bold uppercase text-red-700 hover:bg-red-100 rounded-sm">
                                                Reject & Return to Draft
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('management.approvals.po', $po->id) }}">
                                            @csrf
                                            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold text-xs uppercase tracking-wider rounded-sm hover:bg-emerald-700">
                                                Approve & Create Bill
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="px-4 py-12 text-center sm:px-6">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">All caught up</h3>
                <p class="mt-1 text-sm text-gray-500">No purchase orders pending approval.</p>
            </div>
            @endif
        </div>

        <!-- Supplier Invoices Awaiting Approval -->
        <div class="bg-white shadow sm:rounded-lg border border-gray-100">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Supplier Bills</h3>
                <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20">{{ count($supplierInvoices) }} Pending</span>
            </div>
            
            @if(count($supplierInvoices) > 0)
            <ul role="list" class="divide-y divide-gray-100">
                @foreach($supplierInvoices as $invoice)
                <li class="flex items-center justify-between gap-x-6 py-5 px-4 sm:px-6 hover:bg-gray-50">
                    <div class="min-w-0">
                        <div class="flex items-start gap-x-3">
                            <p class="text-sm font-semibold leading-6 text-gray-900">{{ $invoice->supplier_bill_number ?? 'BILL-'.$invoice->id }}</p>
                        </div>
                        <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                            <p class="whitespace-nowrap">{{ $invoice->supplier->name ?? 'Unknown Supplier' }}</p>
                            <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current"><circle cx="1" cy="1" r="1" /></svg>
                            <p class="truncate">{{ Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-none items-center gap-x-4">
                        <p class="text-sm font-semibold text-gray-900">TZS {{ number_format($invoice->total ?? 0, 2) }}</p>
                        <form method="POST" action="{{ route('management.approvals.invoice', $invoice->id) }}">
                            @csrf
                            <button type="submit" class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-emerald-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block">Approve</button>
                        </form>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="px-4 py-12 text-center sm:px-6">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">All caught up</h3>
                <p class="mt-1 text-sm text-gray-500">No supplier bills pending approval.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
