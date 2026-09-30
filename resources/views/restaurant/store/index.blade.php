@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full">

    <!-- Header & Action Row -->
    <div class="mb-8 border-b-2 border-gray-900 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">Inventory & Store</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">{{ $branch->name }}</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Food Supplies Ledger</h1>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="document.getElementById('wastageModal').style.display = 'flex'" class="px-4 py-2 border-2 border-red-600 bg-red-50 text-red-800 font-bold text-xs uppercase tracking-wider hover:bg-red-100 transition-colors">
                Wastage / Spoilage
            </button>

            <button type="button" onclick="document.getElementById('receiveModal').style.display = 'flex'" class="px-4 py-2 border-2 border-gray-900 bg-gray-900 text-white font-bold text-xs uppercase tracking-wider hover:bg-gray-800 transition-colors">
                Receive Supplies &rarr;
            </button>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="mb-6 p-4 border-2 border-green-600 bg-green-50 text-green-800 font-bold text-sm uppercase tracking-wider">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 border-2 border-red-600 bg-red-50 text-red-800 font-bold text-sm uppercase tracking-wider">
            {{ session('error') }}
        </div>
    @endif

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Store Valuation -->
        <div class="bg-white border-2 border-gray-200 p-5 relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-gray-900"></div>
            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-2">Total Valuation</div>
            <div class="text-2xl font-black text-gray-900 font-mono">TZS {{ number_format($totalValuation, 2) }}</div>
            <p class="text-[10px] uppercase font-bold text-gray-400 mt-2">Capital in stock</p>
        </div>

        <!-- Tracked Items -->
        <div class="bg-white border-2 border-gray-200 p-5 relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-blue-600"></div>
            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-2">Catalog Items</div>
            <div class="text-2xl font-black text-gray-900">{{ $items->count() }} SKUs</div>
            <p class="text-[10px] uppercase font-bold text-gray-400 mt-2">Tracked items</p>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white border-2 border-gray-200 p-5 relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-yellow-600"></div>
            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-2">Low Stock</div>
            <div class="text-2xl font-black text-gray-900">{{ $lowStockCount }} Items</div>
            <p class="text-[10px] uppercase font-bold text-gray-400 mt-2">&le; 10 units left</p>
        </div>

        <!-- Out of Stock -->
        <div class="bg-white border-2 border-gray-200 p-5 relative">
            <div class="absolute top-0 left-0 w-1 h-full bg-red-600"></div>
            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-2">Sold Out</div>
            <div class="text-2xl font-black text-gray-900">{{ $outOfStockCount }} Items</div>
            <p class="text-[10px] uppercase font-bold text-gray-400 mt-2">Depleted stock</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex border-b-2 border-gray-200 mb-6 gap-6">
        <button @click="tab = 'balances'" 
                :class="tab === 'balances' ? 'border-gray-900 text-gray-900 font-black' : 'border-transparent text-gray-500 font-bold hover:text-gray-700'" 
                class="pb-3 border-b-4 uppercase tracking-wider text-sm transition-colors">
            Store Balances
        </button>
        <button @click="tab = 'movements'" 
                :class="tab === 'movements' ? 'border-gray-900 text-gray-900 font-black' : 'border-transparent text-gray-500 font-bold hover:text-gray-700'" 
                class="pb-3 border-b-4 uppercase tracking-wider text-sm transition-colors">
            Movement Audit Trail
        </button>
    </div>

    <!-- Tab Content: Store Balances -->
    <div x-show="tab === 'balances'" x-cloak class="bg-white border-2 border-gray-200 mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-gray-200">
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Item SKU / Name</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Category</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-center">Status</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Store Qty</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Unit Cost</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Valuation</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                        @php
                            $bal = $balances[$item->id] ?? ['quantity' => 0, 'valuation' => 0];
                            $qty = $bal['quantity'];
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-black text-gray-900 text-sm">{{ $item->name }}</div>
                                <div class="text-[10px] font-bold text-gray-500 uppercase mt-0.5">{{ $item->sku }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 border border-gray-300 text-[10px] font-bold uppercase tracking-wider text-gray-700 bg-gray-100">
                                    {{ $item->category->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($qty <= 0)
                                    <span class="px-2 py-0.5 bg-red-50 text-red-800 border border-red-200 font-bold uppercase tracking-wider text-[10px]">Sold Out</span>
                                @elseif($qty <= 10)
                                    <span class="px-2 py-0.5 bg-yellow-50 text-yellow-800 border border-yellow-200 font-bold uppercase tracking-wider text-[10px]">Low</span>
                                @else
                                    <span class="px-2 py-0.5 bg-green-50 text-green-800 border border-green-200 font-bold uppercase tracking-wider text-[10px]">In Stock</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-black font-mono text-gray-900">
                                {{ number_format($qty, 1) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-gray-600">
                                {{ number_format($item->standard_cost, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-black text-gray-900">
                                {{ number_format($bal['valuation'], 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" onclick="openReceiveFor({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->standard_cost }})" class="text-xs font-bold text-blue-600 hover:underline uppercase">
                                        +Rx
                                    </button>
                                    <button type="button" onclick="openWastageFor({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $qty }})" class="text-xs font-bold text-red-600 hover:underline uppercase">
                                        -Waste
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500 font-medium">
                                No items found in the store catalog.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab Content: Movement Audit Trail -->
    <div x-show="tab === 'movements'" x-cloak class="bg-white border-2 border-gray-200 mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-gray-200">
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Date & Time</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Type</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Item</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Qty</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Reference</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Staff</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentMovements as $move)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-xs font-bold text-gray-600 uppercase">
                                {{ $move->created_at->format('d M y, H:i') }}
                            </td>
                            <td class="py-3 px-4">
                                @if($move->type->value === 'receive')
                                    <span class="px-2 py-0.5 bg-green-50 text-green-800 border border-green-200 font-bold uppercase tracking-wider text-[10px]">RX</span>
                                @elseif($move->type->value === 'issue')
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 font-bold uppercase tracking-wider text-[10px]">POS</span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-50 text-red-800 border border-red-200 font-bold uppercase tracking-wider text-[10px]">ADJ/WST</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs font-bold text-gray-900 uppercase">
                                {{ $move->item->name ?? 'Unknown' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-black {{ $move->type->value === 'receive' ? 'text-green-700' : 'text-red-700' }}">
                                {{ $move->type->value === 'receive' ? '+' : '-' }}{{ number_format($move->quantity, 1) }}
                            </td>
                            <td class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">
                                {{ $move->reason ?? ($move->reference_type ? $move->reference_type.' #'.$move->reference_id : 'Store') }}
                            </td>
                            <td class="py-3 px-4 text-xs font-bold text-gray-500 uppercase">
                                {{ $move->user->name ?? 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500 font-medium">
                                No stock movements recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

<!-- Modal: Receive Supplies -->
<div id="receiveModal" style="display: none;" class="fixed inset-0 z-[9999] overflow-y-auto bg-gray-900/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white border-2 border-gray-900 max-w-lg w-full p-6 shadow-2xl relative mx-auto my-auto">
        <div class="flex items-center justify-between border-b-2 border-gray-200 pb-4 mb-4">
            <div>
                <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">Receive Stock</h3>
            </div>
            <button type="button" onclick="document.getElementById('receiveModal').style.display = 'none'" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
        </div>

        <form method="POST" action="{{ route('restaurant.store.receive') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="branch_id" value="{{ $branch->id }}">

            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Catalog Item *</label>
                @if($items->isEmpty())
                    <div class="p-3 border-2 border-orange-300 bg-orange-50 text-sm font-bold text-orange-800 mb-2">
                        You have no items in your catalog. <br>
                        <a href="{{ route('restaurant.menu') }}" class="underline text-orange-900 mt-1 inline-block">Go to Products & Menu to add items</a>
                    </div>
                @else
                <select name="item_id" id="receiveItemId" required class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                    <option value="" disabled selected>Select an item...</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-cost="{{ $item->standard_cost }}">{{ $item->name }} ({{ $item->sku }})</option>
                    @endforeach
                </select>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Quantity Received *</label>
                    <input type="number" step="0.1" min="0.1" name="quantity" required class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold font-mono text-gray-900 focus:outline-none focus:border-gray-900">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Unit Cost (TZS)</label>
                    <input type="number" step="0.01" min="0" name="unit_cost" id="receiveUnitCost" class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold font-mono text-gray-900 focus:outline-none focus:border-gray-900">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Supplier</label>
                    <input type="text" name="supplier_name" class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Delivery Ref</label>
                    <input type="text" name="delivery_note" class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t-2 border-gray-200 mt-4">
                <button type="button" onclick="document.getElementById('receiveModal').style.display = 'none'" class="px-4 py-2 border-2 border-gray-300 text-xs font-bold uppercase text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" @if($items->isEmpty()) disabled @endif class="px-5 py-2 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-xs font-bold uppercase text-white disabled:opacity-50 disabled:cursor-not-allowed">
                    Confirm Receipt
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Record Wastage -->
<div id="wastageModal" style="display: none;" class="fixed inset-0 z-[9999] overflow-y-auto bg-gray-900/50 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white border-2 border-gray-900 max-w-lg w-full p-6 shadow-2xl relative mx-auto my-auto">
        <div class="flex items-center justify-between border-b-2 border-gray-200 pb-4 mb-4">
            <div>
                <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">Record Wastage</h3>
            </div>
            <button type="button" onclick="document.getElementById('wastageModal').style.display = 'none'" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
        </div>

        <form method="POST" action="{{ route('restaurant.store.wastage') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="branch_id" value="{{ $branch->id }}">

            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Item to Write Off *</label>
                @if($items->isEmpty())
                    <div class="p-3 border-2 border-orange-300 bg-orange-50 text-sm font-bold text-orange-800 mb-2">
                        You have no items in your catalog.
                    </div>
                @else
                <select name="item_id" id="wastageItemId" required class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                    <option value="" disabled selected>Select an item...</option>
                    @foreach($items as $item)
                        @php $q = $balances[$item->id]['quantity'] ?? 0; @endphp
                        <option value="{{ $item->id }}" data-avail="{{ $q }}">{{ $item->name }} (Store: {{ $q }})</option>
                    @endforeach
                </select>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Quantity *</label>
                    <input type="number" step="0.1" min="0.1" name="quantity" required class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold font-mono text-gray-900 focus:outline-none focus:border-gray-900">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Reason *</label>
                    <select name="reason" required class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                        <option value="Expired Product">Expired Product</option>
                        <option value="Spoiled / Rotten Produce">Spoiled / Rotten Produce</option>
                        <option value="Spillage / Kitchen Burnt">Spillage / Kitchen Burnt</option>
                        <option value="Customer Return">Customer Return</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t-2 border-gray-200 mt-4">
                <button type="button" onclick="document.getElementById('wastageModal').style.display = 'none'" class="px-4 py-2 border-2 border-gray-300 text-xs font-bold uppercase text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" @if($items->isEmpty()) disabled @endif class="px-5 py-2 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-xs font-bold uppercase text-white disabled:opacity-50 disabled:cursor-not-allowed">
                    Write Off
                </button>
            </div>
        </form>
    </div>
</div>

</div> <!-- Close max-w-7xl div -->

<script>
    // Note: We use purely vanilla JS for modals so it isn't affected by Tailwind specificity or Alpine.
    function openReceiveFor(itemId, itemName, cost) {
        const select = document.getElementById('receiveItemId');
        const costInput = document.getElementById('receiveUnitCost');
        if (select) select.value = itemId;
        if (costInput) costInput.value = cost;
        document.getElementById('receiveModal').style.display = 'flex';
    }

    function openWastageFor(itemId, itemName, avail) {
        const select = document.getElementById('wastageItemId');
        if (select) select.value = itemId;
        document.getElementById('wastageModal').style.display = 'flex';
    }
</script>
@endsection
