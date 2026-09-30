@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full">
    <!-- Header -->
    <div class="mb-8 border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">Procurement Module</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">Purchase Requests</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Purchase Requests</h1>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('restaurant.purchasing.requests.create') }}" class="px-5 py-2.5 bg-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors shadow-sm flex items-center gap-2 rounded-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New Request
            </a>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex border-b-2 border-gray-200 mb-6 gap-6">
        <a href="{{ route('restaurant.purchasing.requests') }}" 
           class="{{ request()->routeIs('restaurant.purchasing.requests*') ? 'border-gray-900 text-gray-900 font-black' : 'border-transparent text-gray-500 font-bold hover:text-gray-700' }} pb-3 border-b-4 uppercase tracking-wider text-sm transition-colors">
            Purchase Requests
        </a>
        <a href="{{ route('restaurant.purchasing.orders') }}" 
           class="{{ request()->routeIs('restaurant.purchasing.orders*') ? 'border-gray-900 text-gray-900 font-black' : 'border-transparent text-gray-500 font-bold hover:text-gray-700' }} pb-3 border-b-4 uppercase tracking-wider text-sm transition-colors">
            Purchase Orders
        </a>
        <a href="{{ route('restaurant.purchasing.suppliers') }}" 
           class="{{ request()->routeIs('restaurant.purchasing.suppliers*') ? 'border-gray-900 text-gray-900 font-black' : 'border-transparent text-gray-500 font-bold hover:text-gray-700' }} pb-3 border-b-4 uppercase tracking-wider text-sm transition-colors">
            Suppliers Directory
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 border border-green-200 bg-green-50 text-green-800 font-bold text-sm uppercase tracking-wider rounded-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white border border-gray-200 rounded-sm shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest">My Requests</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-white border-b border-gray-200">
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">PO Number</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Supplier</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Amount (TZS)</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-center">Status</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Date</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $purchaseRequest)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-4 font-black font-mono text-gray-900">
                            {{ $purchaseRequest->reference_number }}
                        </td>
                        <td class="py-4 px-4 text-xs font-bold text-gray-700 uppercase">
                            {{ $purchaseRequest->supplier->name ?? 'Unknown Supplier' }}
                        </td>
                        <td class="py-4 px-4 text-right font-mono font-black text-gray-900">
                            {{ number_format($purchaseRequest->total, 2) }}
                        </td>
                        <td class="py-4 px-4 text-center">
                            @if($purchaseRequest->status->value == 'submitted' || $purchaseRequest->status->value == 'pending')
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-bold uppercase tracking-wider rounded-sm">Pending</span>
                            @elseif($purchaseRequest->status->value == 'approved')
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold uppercase tracking-wider rounded-sm">Approved</span>
                            @elseif($purchaseRequest->status->value == 'completed')
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold uppercase tracking-wider rounded-sm">Completed</span>
                            @else
                                <span class="px-2.5 py-1 bg-gray-100 text-gray-600 border border-gray-300 text-[10px] font-bold uppercase tracking-wider rounded-sm">{{ $purchaseRequest->status->value }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-xs font-bold text-gray-500 uppercase">
                            {{ $purchaseRequest->created_at->format('d M Y') }}
                        </td>
                        <td class="py-4 px-4 text-right">
                            <a onclick="document.getElementById('viewDetailsModal_{{ $purchaseRequest->id }}').classList.remove('hidden')" class="text-xs font-bold text-blue-600 hover:underline uppercase mr-3 cursor-pointer">View Details</a>
                            @if($purchaseRequest->status->value == 'draft')
                                <a href="{{ route('restaurant.purchasing.requests.edit', $purchaseRequest->id) }}" class="text-xs font-bold text-amber-600 hover:underline uppercase mr-3">Edit</a>
                            @endif
                            @if($purchaseRequest->status->value == 'draft')
                                <form method="POST" action="{{ route('restaurant.purchasing.submit-draft', $purchaseRequest->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-gray-900 hover:underline uppercase mr-3">Submit</button>
                                </form>
                            @endif
                            <!-- View Details Modal -->
        <div id="viewDetailsModal_{{ $purchaseRequest->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white border border-gray-300 rounded-sm max-w-2xl w-full p-0 shadow-2xl">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">Purchase Request Details</h3>
                        <p class="text-xs font-bold text-gray-500 uppercase mt-1">PO Number: {{ $purchaseRequest->reference_number }}</p>
                    </div>
                    <button type="button" onclick="document.getElementById('viewDetailsModal_{{ $purchaseRequest->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
                </div>
                
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-xs font-bold text-gray-500 uppercase">Supplier</p>
                        <p class="text-sm font-bold text-gray-900">{{ $purchaseRequest->supplier->name ?? 'Unknown Supplier' }}</p>
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
                                @forelse($purchaseRequest->lines as $line)
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
                                    <td class="py-2 px-3 text-right font-mono font-black text-gray-900">{{ number_format($purchaseRequest->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="document.getElementById('viewDetailsModal_{{ $purchaseRequest->id }}').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-xs font-bold uppercase text-gray-700 hover:bg-gray-50 rounded-sm">
                            Close
                        </button>
                        
                        @if($purchaseRequest->status->value === 'draft')
                        <form method="POST" action="{{ route('restaurant.purchasing.submit-draft', $purchaseRequest->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-5 py-2 bg-gray-900 text-white font-bold text-xs uppercase tracking-wider rounded-sm hover:bg-gray-800">
                                Submit for Approval &rarr;
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if($purchaseRequest->status->value == 'approved' || $purchaseRequest->status->value == 'partially_received')
                            <button onclick="document.getElementById('receivePOModal_{{ $purchaseRequest->id }}').classList.remove('hidden')" class="text-xs font-bold text-emerald-600 hover:underline uppercase">Receive Goods</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-500 font-medium">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            You haven't made any purchase requests yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($requests, 'hasPages') && $requests->hasPages())
        <div class="p-4 border-t border-gray-200 bg-gray-50">
            {{ $requests->links() }}
        </div>
        @endif
    </div>

    <!-- Modals for Receiving POs will be generated here -->
    @foreach($requests as $purchaseRequest)
        <!-- View Details Modal -->
        <div id="viewDetailsModal_{{ $purchaseRequest->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white border border-gray-300 rounded-sm max-w-2xl w-full p-0 shadow-2xl">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">Purchase Request Details</h3>
                        <p class="text-xs font-bold text-gray-500 uppercase mt-1">PO Number: {{ $purchaseRequest->reference_number }}</p>
                    </div>
                    <button type="button" onclick="document.getElementById('viewDetailsModal_{{ $purchaseRequest->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
                </div>
                
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-xs font-bold text-gray-500 uppercase">Supplier</p>
                        <p class="text-sm font-bold text-gray-900">{{ $purchaseRequest->supplier->name ?? 'Unknown Supplier' }}</p>
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
                                @forelse($purchaseRequest->lines as $line)
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
                                    <td class="py-2 px-3 text-right font-mono font-black text-gray-900">{{ number_format($purchaseRequest->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="document.getElementById('viewDetailsModal_{{ $purchaseRequest->id }}').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-xs font-bold uppercase text-gray-700 hover:bg-gray-50 rounded-sm">
                            Close
                        </button>
                        
                        @if($purchaseRequest->status->value === 'draft')
                        <form method="POST" action="{{ route('restaurant.purchasing.submit-draft', $purchaseRequest->id) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-5 py-2 bg-gray-900 text-white font-bold text-xs uppercase tracking-wider rounded-sm hover:bg-gray-800">
                                Submit for Approval &rarr;
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if($purchaseRequest->status->value == 'approved' || $purchaseRequest->status->value == 'partially_received')
        <div id="receivePOModal_{{ $purchaseRequest->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white border border-gray-300 rounded-sm max-w-2xl w-full p-0 shadow-2xl">
                <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">Receive Purchase Order</h3>
                        <p class="text-xs font-bold text-gray-500 uppercase mt-1">PO Number: {{ $purchaseRequest->reference_number }}</p>
                    </div>
                    <button type="button" onclick="document.getElementById('receivePOModal_{{ $purchaseRequest->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
                </div>
                
                <div class="p-6">
                    <form method="POST" action="{{ route('restaurant.purchasing.receive', $purchaseRequest->id) }}">
                        @csrf
                        <p class="text-sm text-gray-600 mb-4">Please verify the quantities received against the requested amounts. Any items received will be immediately added to your Store Balances.</p>
                        
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Destination Store <span class="text-red-500">*</span></label>
                            <select name="location_id" required class="w-full border border-gray-300 p-2 text-sm focus:border-gray-900 focus:outline-none">
                                <option value="">-- Select Store Location --</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="border border-gray-200 rounded-sm overflow-hidden mb-6">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider">Item</th>
                                        <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right">Requested Qty</th>
                                        <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right">Pending Qty</th>
                                        <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right">Received Qty</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($purchaseRequest->lines as $line)
                                    <tr>
                                        <td class="py-2 px-3 font-bold text-gray-900">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="receive_line[{{ $line->id }}]" value="1" checked class="rounded-sm border-gray-300 text-gray-900 focus:ring-gray-900">
                                                {{ $line->item->name ?? 'Unknown' }}
                                            </label>
                                        </td>
                                        <td class="py-2 px-3 text-right font-mono">{{ number_format($line->quantity, 1) }}</td>
                                        <td class="py-2 px-3 text-right font-mono text-amber-600">{{ number_format($line->quantity - $line->received_quantity, 1) }}</td>
                                        <td class="py-2 px-3 text-right">
                                            <input type="number" name="received_qty[{{ $line->id }}]" min="0" max="{{ $line->quantity - $line->received_quantity }}" step="0.1" value="{{ $line->quantity - $line->received_quantity }}" class="w-20 border border-gray-300 px-2 py-1 text-right font-mono text-sm focus:outline-none focus:border-gray-900">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">No items on this request.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" onclick="document.getElementById('receivePOModal_{{ $purchaseRequest->id }}').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-xs font-bold uppercase text-gray-700 hover:bg-gray-50 rounded-sm">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold text-xs uppercase tracking-wider rounded-sm hover:bg-emerald-700">
                                Confirm Receipt to Inventory
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach

</div>
@endsection
