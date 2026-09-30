@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full" x-data="{ restockModalOpen: false, restockItem: null, restockQty: 50, restockUrl: '', addProductModalOpen: false, editProductModalOpen: false, editItem: null, editUrl: '', searchQuery: '', searchResults: [], isSearching: false, selectedExistingItem: null, searchExisting() { if(this.searchQuery.length < 2) { this.searchResults = []; return; } this.isSearching = true; fetch('{{ route('restaurant.menu.search') }}?query=' + encodeURIComponent(this.searchQuery)).then(res => res.json()).then(data => { this.searchResults = data; this.isSearching = false; }); }, selectItem(item) { this.selectedExistingItem = item; this.searchResults = []; this.searchQuery = item.name; } }">
    <div class="mb-8 border-b-2 border-gray-900 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">Station Menu Catalog</span>
                <span class="text-[10px] font-bold bg-gray-100 text-gray-900 px-2 py-0.5 border border-gray-300">{{ $items->total() }} items</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Products & Menu</h1>
        </div>
        
        <div class="flex items-center gap-2">
            <button @click="addProductModalOpen = true" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors flex items-center gap-2">
                Add Item &rarr;
            </button>
        </div>
    </div>

    <!-- Category Filter Pills + Search -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50 p-4 border-2 border-gray-200">
        <div class="flex items-center gap-2 overflow-x-auto scrollbar-none pb-2 md:pb-0">
            <a href="{{ route('restaurant.menu') }}" 
               class="px-4 py-2 border-2 text-xs font-bold uppercase tracking-wider whitespace-nowrap transition-colors flex items-center gap-1.5 {{ !request('category') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' }}">
                All <span class="opacity-70">({{ $items->total() }})</span>
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('restaurant.menu', ['category' => $cat->id, 'search' => $search ?? '']) }}" 
               class="px-4 py-2 border-2 text-xs font-bold uppercase tracking-wider whitespace-nowrap transition-colors flex items-center gap-1.5 {{ request('category') == $cat->id ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-700 hover:bg-gray-100 border-gray-300' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
        
        <form method="GET" action="{{ route('restaurant.menu') }}" class="flex items-center gap-2 shrink-0">
            @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="relative">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="SEARCH MENU..." 
                       class="w-full sm:w-64 bg-white border-2 border-gray-300 pl-10 pr-4 py-2 text-sm font-bold text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-900">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button type="submit" class="px-4 py-2 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-xs font-bold text-white uppercase tracking-wider transition-colors">
                Search
            </button>
            @if($search)
            <a href="{{ route('restaurant.menu', ['category' => request('category')]) }}" class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-900 uppercase tracking-wider">
                Clear
            </a>
            @endif
        </form>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 border-2 border-green-600 bg-green-50 text-green-800 font-bold text-sm uppercase tracking-wider">
        {{ session('success') }}
    </div>
    @endif

    <!-- Menu Items Table -->
    <div class="bg-white border-2 border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b-2 border-gray-200">
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Item & Description</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">SKU</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Category</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Price (TZS)</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Unit Cost</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Stock</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs">Status</th>
                        <th class="py-3 px-4 font-bold text-gray-600 uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($items as $item)
                    @php
                        $stockQty = $stockBalances[$item->id] ?? 0;
                        $margin = $item->base_price > 0 ? round((($item->base_price - ($item->standard_cost ?? 0)) / $item->base_price) * 100) : 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4">
                            <div class="font-black text-gray-900 text-sm">{{ $item->name }}</div>
                            <div class="text-gray-500 text-[10px] font-bold uppercase mt-0.5 line-clamp-1">{{ $item->description ?? 'No description' }}</div>
                        </td>
                        <td class="py-3 px-4 text-xs font-mono font-bold text-gray-700">
                            {{ $item->sku }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 border border-gray-300 text-[10px] font-bold uppercase tracking-wider text-gray-700 bg-gray-100">
                                {{ $item->category->name ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-sm font-black text-gray-900 font-mono">
                            {{ number_format($item->base_price, 2) }}
                        </td>
                        <td class="py-3 px-4 text-xs font-mono text-gray-500">
                            {{ number_format($item->standard_cost ?? 0, 2) }}
                            <span class="text-[10px] text-green-700 font-bold block">{{ $margin }}% margin</span>
                        </td>
                        <td class="py-3 px-4 text-xs font-mono font-bold">
                            @if($stockQty > 15)
                                <span class="px-2 py-0.5 bg-green-50 text-green-800 border border-green-200 uppercase text-[10px]">
                                    {{ (int) $stockQty }} IN STOCK
                                </span>
                            @elseif($stockQty > 0)
                                <span class="px-2 py-0.5 bg-orange-50 text-orange-800 border border-orange-200 uppercase text-[10px]">
                                    {{ (int) $stockQty }} LOW
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-red-50 text-red-800 border border-red-200 uppercase text-[10px]">
                                    0 SOLD OUT
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs">
                            @if($item->status)
                                <span class="px-2 py-0.5 bg-green-50 text-green-800 border border-green-200 font-bold uppercase tracking-wider text-[10px]">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-red-50 text-red-800 border border-red-200 font-bold uppercase tracking-wider text-[10px]">
                                    Disabled
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button type="button" 
                                        @click="editItem = {{ json_encode(['id' => $item->id, 'name' => $item->name, 'sku' => $item->sku, 'description' => $item->description, 'base_price' => (float)$item->base_price, 'standard_cost' => (float)$item->standard_cost, 'category_id' => $item->category_id, 'type' => $item->type->value ?? $item->type]) }}; editUrl = '{{ route('restaurant.menu.update', $item->id) }}'; editProductModalOpen = true;"
                                        class="text-xs font-bold text-blue-600 hover:underline uppercase">
                                    Edit
                                </button>
                                <button type="button" 
                                        @click="restockItem = {{ json_encode(['id' => $item->id, 'name' => $item->name, 'sku' => $item->sku, 'current_stock' => (int)$stockQty]) }}; restockUrl = '{{ route('restaurant.menu.restock', $item->id) }}'; restockModalOpen = true;"
                                        class="text-xs font-bold text-gray-900 hover:underline uppercase">
                                    Stock+
                                </button>
                                <form method="POST" action="{{ route('restaurant.menu.toggle', $item->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-gray-500 hover:text-gray-900 hover:underline uppercase">
                                        {{ $item->status ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500 font-medium">
                            No menu items cataloged yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="p-4 border-t-2 border-gray-200 bg-gray-50">
            {{ $items->links() }}
        </div>
        @endif

        <!-- Restock Modal -->
        <div x-show="restockModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div @click.away="restockModalOpen = false" class="bg-white border-2 border-gray-900 max-w-md w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b-2 border-gray-200 mb-4">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">Receive Stock</h3>
                        <p class="text-xs font-bold text-gray-500 uppercase mt-1" x-text="restockItem ? restockItem.name : ''"></p>
                    </div>
                    <button type="button" @click="restockModalOpen = false" class="text-gray-400 hover:text-gray-900 text-2xl font-bold">&times;</button>
                </div>

                <form :action="restockUrl" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Quantity to Add *</label>
                        <input type="number" 
                               name="quantity" 
                               min="1" 
                               required 
                               x-model="restockQty"
                               class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-sm font-bold font-mono text-gray-900 focus:outline-none focus:border-gray-900">
                        <p class="text-[10px] font-bold text-gray-500 uppercase mt-1">Current Stock: <span class="text-gray-900" x-text="restockItem ? restockItem.current_stock : 0"></span></p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Reason / Batch Ref</label>
                        <input type="text" 
                               name="reason" 
                               value="Daily Kitchen Prep Restock" 
                               class="w-full bg-white border-2 border-gray-300 px-3 py-2 text-xs font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t-2 border-gray-200 mt-4">
                        <button type="button" @click="restockModalOpen = false" class="px-4 py-2 border-2 border-gray-300 text-xs font-bold uppercase text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-xs font-bold uppercase text-white">
                            Confirm Restock
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div x-show="addProductModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div @click.away="addProductModalOpen = false" class="bg-white border-2 border-gray-900 max-w-2xl w-full p-0 shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between p-6 border-b-2 border-gray-200 bg-gray-50">
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">Add / Configure Product</h3>
                    <button type="button" @click="addProductModalOpen = false; selectedExistingItem = null; searchQuery = ''; searchResults = [];" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <!-- Search Section -->
                    <div class="mb-6 pb-6 border-b-2 border-gray-100" x-show="!selectedExistingItem">
                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-2">Search Existing Item (By Name or SKU)</label>
                        <div class="relative">
                            <input type="text" x-model="searchQuery" @input.debounce.300ms="searchExisting()" class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-3 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900" placeholder="Type to search inventory items...">
                            <div x-show="isSearching" class="absolute right-3 top-3 text-gray-400 text-xs font-bold uppercase">Searching...</div>
                            
                            <!-- Search Results Dropdown -->
                            <div x-show="searchResults.length > 0" class="absolute z-10 w-full mt-1 bg-white border-2 border-gray-900 shadow-xl max-h-60 overflow-y-auto">
                                <template x-for="item in searchResults" :key="item.id">
                                    <div @click="selectItem(item)" class="p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer flex justify-between items-center">
                                        <div>
                                            <div class="font-bold text-sm text-gray-900" x-text="item.name"></div>
                                            <div class="text-[10px] text-gray-500 font-mono" x-text="item.sku"></div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs font-bold text-gray-700">Stock: <span x-text="item.stock"></span></div>
                                            <div class="text-[10px] font-bold" :class="item.can_be_sold ? 'text-green-600' : 'text-orange-600'" x-text="item.can_be_sold ? 'Already Sellable' : 'Not Sellable'"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Duplicate Warning for New Items -->
                            <div x-show="searchResults.length > 0 && searchQuery.length > 2" class="mt-2 p-3 bg-orange-50 border-2 border-orange-200 text-orange-800 text-xs font-bold">
                                Warning: Items with similar names already exist! Please select one from the list above if it's the same physical item to prevent duplicate stock records.
                            </div>
                        </div>
                    </div>

                    <!-- Selected Item Banner -->
                    <div x-show="selectedExistingItem" class="mb-6 p-4 bg-blue-50 border-2 border-blue-200 flex justify-between items-center">
                        <div>
                            <div class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-1">Configuring Existing Item</div>
                            <div class="font-bold text-gray-900 text-sm" x-text="selectedExistingItem?.name"></div>
                            <div class="text-xs text-gray-600 font-mono" x-text="selectedExistingItem?.sku"></div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-gray-700">Current Stock: <span x-text="selectedExistingItem?.stock"></span></div>
                            <button type="button" @click="selectedExistingItem = null; searchQuery = ''" class="text-[10px] font-bold text-blue-600 hover:underline uppercase mt-1">Change Selection</button>
                        </div>
                    </div>

                    <form action="{{ route('restaurant.menu.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="existing_item_id" :value="selectedExistingItem ? selectedExistingItem.id : ''">
                        
                        <div class="grid grid-cols-2 gap-5">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Item Name *</label>
                                <input type="text" name="name" :value="selectedExistingItem ? selectedExistingItem.name : searchQuery" :readonly="selectedExistingItem != null" required class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900" :class="selectedExistingItem ? 'bg-gray-50' : ''">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">SKU / Code *</label>
                                <input type="text" name="sku" :value="selectedExistingItem ? selectedExistingItem.sku : ''" :readonly="selectedExistingItem != null" required class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold font-mono text-gray-900 focus:outline-none focus:border-gray-900" placeholder="e.g. MEAL-001" :class="selectedExistingItem ? 'bg-gray-50' : ''">
                            </div>
                            
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Category *</label>
                                <select name="category_id" required class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900" :value="selectedExistingItem ? selectedExistingItem.category_id : ''">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Item Type *</label>
                                <select name="type" required class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                                    <option value="physical">Physical Item (Inventory)</option>
                                    <option value="service">Service (No Inventory)</option>
                                    <option value="package">Package / Combo</option>
                                </select>
                            </div>

                            <div class="col-span-2 sm:col-span-1" x-show="!selectedExistingItem">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Unit of Measure *</label>
                                <select name="unit_id" :required="!selectedExistingItem" class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Selling Price (TZS) *</label>
                                <input type="number" step="0.01" name="base_price" :value="selectedExistingItem ? selectedExistingItem.base_price : ''" required class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold font-mono text-gray-900 focus:outline-none focus:border-gray-900">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Standard Cost (TZS) *</label>
                                <input type="number" step="0.01" name="standard_cost" :value="selectedExistingItem ? selectedExistingItem.standard_cost : ''" required class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold font-mono text-gray-900 focus:outline-none focus:border-gray-900">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Description</label>
                                <textarea name="description" rows="2" class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900"></textarea>
                            </div>

                            <div class="col-span-2 sm:col-span-1 flex items-center space-x-2 mt-2">
                                <input type="checkbox" name="can_be_sold" id="can_be_sold_add" value="1" checked class="w-5 h-5 text-gray-900 border-2 border-gray-300 focus:ring-gray-900">
                                <label for="can_be_sold_add" class="text-xs font-bold text-gray-700 uppercase cursor-pointer">Can be Sold (POS)</label>
                            </div>
                            
                            <div class="col-span-2 sm:col-span-1 flex items-center space-x-2 mt-2">
                                <input type="checkbox" name="can_be_purchased" id="can_be_purchased_add" value="1" :checked="selectedExistingItem ? selectedExistingItem.can_be_purchased : true" class="w-5 h-5 text-gray-900 border-2 border-gray-300 focus:ring-gray-900">
                                <label for="can_be_purchased_add" class="text-xs font-bold text-gray-700 uppercase cursor-pointer">Can be Purchased (LPO)</label>
                            </div>
                            
                            <div class="col-span-2 sm:col-span-1 flex items-center space-x-2 mt-2">
                                <input type="checkbox" name="track_inventory" id="track_inventory_add" value="1" checked class="w-5 h-5 text-gray-900 border-2 border-gray-300 focus:ring-gray-900">
                                <label for="track_inventory_add" class="text-xs font-bold text-gray-700 uppercase cursor-pointer">Track Inventory</label>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-5 mt-2 border-t-2 border-gray-200">
                            <button type="button" @click="if(!selectedExistingItem) { document.getElementById('new-item-btn').click(); }" id="new-item-btn" class="hidden">Create New Item Instead</button>
                            
                            <div x-show="!selectedExistingItem" class="text-[10px] text-gray-500 font-bold uppercase">Creating a completely new item</div>
                            <div x-show="selectedExistingItem" class="text-[10px] text-blue-600 font-bold uppercase">Updating existing inventory item</div>
                            
                            <div class="flex gap-3">
                                <button type="button" @click="addProductModalOpen = false; selectedExistingItem = null; searchQuery = ''; searchResults = [];" class="px-5 py-2.5 border-2 border-gray-300 hover:bg-gray-50 text-sm font-bold uppercase tracking-wider text-gray-700 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-2.5 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-sm font-bold uppercase tracking-wider text-white transition-all" x-text="selectedExistingItem ? 'Update & Configure' : 'Create New Item'">
                                    Create Item
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div x-show="editProductModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
            <div @click.away="editProductModalOpen = false" class="bg-white border-2 border-gray-900 max-w-2xl w-full p-0 shadow-2xl flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between p-6 border-b-2 border-gray-200 bg-gray-50">
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">Edit Menu Item</h3>
                    <button type="button" @click="editProductModalOpen = false" class="text-gray-400 hover:text-gray-900 text-2xl font-bold transition-colors">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto">
                    <form :action="editUrl" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-2 gap-5">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Item Name *</label>
                                <input type="text" name="name" x-model="editItem ? editItem.name : ''" required class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">SKU / Code (Read-Only)</label>
                                <input type="text" disabled x-model="editItem ? editItem.sku : ''" class="w-full bg-gray-100 border-2 border-gray-200 rounded-none px-4 py-2 text-sm font-bold font-mono text-gray-500 cursor-not-allowed">
                            </div>
                            
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Category *</label>
                                <select name="category_id" x-model="editItem ? editItem.category_id : ''" required class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Item Type *</label>
                                <select name="type" x-model="editItem ? editItem.type : 'physical'" required class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                                    <option value="physical">Physical Item (Inventory)</option>
                                    <option value="service">Service (No Inventory)</option>
                                    <option value="package">Package / Combo</option>
                                </select>
                            </div>

                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Selling Price (TZS) *</label>
                                <input type="number" step="0.01" name="base_price" x-model="editItem ? editItem.base_price : ''" required class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold font-mono text-gray-900 focus:outline-none focus:border-gray-900">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Standard Cost (TZS) *</label>
                                <input type="number" step="0.01" name="standard_cost" x-model="editItem ? editItem.standard_cost : ''" required class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold font-mono text-gray-900 focus:outline-none focus:border-gray-900">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Description</label>
                                <textarea name="description" rows="2" x-model="editItem ? editItem.description : ''" class="w-full bg-white border-2 border-gray-300 rounded-none px-4 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900"></textarea>
                            </div>

                            <div class="col-span-2 sm:col-span-1 flex items-center space-x-2 mt-2">
                                <input type="checkbox" name="can_be_sold" id="can_be_sold_edit" value="1" x-bind:checked="editItem ? editItem.can_be_sold : true" class="w-5 h-5 text-gray-900 border-2 border-gray-300 focus:ring-gray-900">
                                <label for="can_be_sold_edit" class="text-xs font-bold text-gray-700 uppercase cursor-pointer">Can be Sold (POS)</label>
                            </div>
                            
                            <div class="col-span-2 sm:col-span-1 flex items-center space-x-2 mt-2">
                                <input type="checkbox" name="can_be_purchased" id="can_be_purchased_edit" value="1" x-bind:checked="editItem ? editItem.can_be_purchased : true" class="w-5 h-5 text-gray-900 border-2 border-gray-300 focus:ring-gray-900">
                                <label for="can_be_purchased_edit" class="text-xs font-bold text-gray-700 uppercase cursor-pointer">Can be Purchased (LPO)</label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-5 mt-2 border-t-2 border-gray-200">
                            <button type="button" @click="editProductModalOpen = false" class="px-5 py-2.5 border-2 border-gray-300 hover:bg-gray-50 text-sm font-bold uppercase tracking-wider text-gray-700 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-6 py-2.5 border-2 border-gray-900 bg-gray-900 hover:bg-gray-800 text-sm font-bold uppercase tracking-wider text-white transition-all">
                                Update Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
