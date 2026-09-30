@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full">
    <!-- Header -->
    <div class="mb-8 border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">Procurement Module</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">New Request</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Create Purchase Order</h1>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('restaurant.purchasing.requests') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold text-sm uppercase tracking-wider hover:bg-gray-50 transition-colors shadow-sm rounded-sm">
                Cancel
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

    <!-- Form -->
    <div x-data="purchaseOrderForm()" class="bg-white border border-gray-200 rounded-sm shadow-sm overflow-hidden mb-8">
        <form method="POST" action="{{ route('restaurant.purchasing.requests.update', $purchaseRequest->id) }}">
            @method('PUT')
            @csrf
            <input type="hidden" name="business_unit_id" value="{{ $businessUnit->id }}">
            
            <!-- Supplier Section -->
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4">Supplier Information</h3>
                <div class="max-w-md">
                    <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1" for="supplier_id">Select Supplier *</label>
                    <select id="supplier_id" name="supplier_id" class="w-full bg-white border border-gray-300 rounded-sm px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 shadow-sm" required>
                        <option value="">-- Choose Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ $purchaseRequest->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Items Section -->
            <div class="p-6">
                <div class="flex justify-between items-end mb-4">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Order Line Items</h3>
                    <button type="button" @click="addLine()" class="px-3 py-1.5 bg-gray-900 text-white font-bold text-xs uppercase tracking-wider hover:bg-gray-800 transition-colors rounded-sm shadow-sm">
                        + Add Item
                    </button>
                </div>

                <div class="border border-gray-200 rounded-sm overflow-hidden">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider w-1/4">Item Name</th>
                                <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider w-1/6">Category</th>
                                <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider w-1/6">Unit</th>
                                <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right w-1/12">Qty</th>
                                <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right w-1/6">Est. Unit Cost</th>
                                <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-right w-1/6">Total</th>
                                <th class="py-2 px-3 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-center w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(line, index) in lines" :key="line.id">
                                <tr>
                                    <td class="py-2 px-3">
                                        <input type="text" :name="`items[${index}][name]`" x-model="line.name" placeholder="e.g. Mchele, Sahani..." class="w-full bg-white border border-gray-300 rounded-sm px-2 py-1.5 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 shadow-sm" required>
                                    </td>
                                    <td class="py-2 px-3">
                                        <select :name="`items[${index}][category_id]`" x-model="line.category_id" class="w-full bg-white border border-gray-300 rounded-sm px-2 py-1.5 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 shadow-sm" required>
                                            <option value="">-- Category --</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2 px-3">
                                        <select :name="`items[${index}][unit_id]`" x-model="line.unit_id" class="w-full bg-white border border-gray-300 rounded-sm px-2 py-1.5 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 shadow-sm" required>
                                            <option value="">-- Unit --</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->code }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2 px-3">
                                        <input type="number" :name="`items[${index}][quantity]`" x-model="line.quantity" min="0.1" step="0.1" class="w-full bg-white border border-gray-300 rounded-sm px-2 py-1.5 text-right font-mono text-sm focus:outline-none focus:border-gray-900 shadow-sm" required>
                                    </td>
                                    <td class="py-2 px-3">
                                        <input type="number" :name="`items[${index}][unit_price]`" x-model="line.price" min="0" step="0.01" class="w-full bg-white border border-gray-300 rounded-sm px-2 py-1.5 text-right font-mono text-sm focus:outline-none focus:border-gray-900 shadow-sm" required>
                                    </td>
                                    <td class="py-2 px-3 text-right font-mono font-black text-gray-900" x-text="formatNumber(line.quantity * line.price)">
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <button type="button" @click="removeLine(index)" class="text-red-500 hover:text-red-700 font-bold text-lg" title="Remove">&times;</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="lines.length === 0">
                                <td colspan="7" class="py-8 text-center text-gray-500">
                                    Click "+ Add Item" to define your request items.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="5" class="py-3 px-4 text-right font-black text-gray-900 uppercase tracking-widest text-sm">Grand Total</td>
                                <td class="py-3 px-3 text-right font-mono font-black text-lg text-gray-900" x-text="'TZS ' + formatNumber(calculateTotal())"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3 bg-gray-50">
                <button type="submit" name="action" value="draft" :disabled="lines.length === 0" :class="lines.length === 0 ? 'opacity-50 cursor-not-allowed' : ''" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold text-sm uppercase tracking-wider hover:bg-gray-50 transition-colors shadow-sm rounded-sm">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="submit" :disabled="lines.length === 0" :class="lines.length === 0 ? 'opacity-50 cursor-not-allowed' : ''" class="px-6 py-2.5 bg-gray-900 text-white font-bold text-sm uppercase tracking-wider rounded-sm hover:bg-gray-800 transition-colors shadow-sm">
                    Submit Request &rarr;
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('purchaseOrderForm', () => ({
            lines: {!! json_encode($purchaseRequest->lines->map(function($line) {
                return [
                    'id' => $line->id,
                    'name' => $line->item->name ?? '',
                    'category_id' => $line->item->category_id ?? '',
                    'unit_id' => $line->item->unit_id ?? '',
                    'quantity' => $line->quantity,
                    'price' => $line->unit_price
                ];
            })) !!},
            nextId: 1,
            
            addLine() {
                this.lines.push({
                    id: this.nextId++,
                    name: '',
                    category_id: '',
                    unit_id: '',
                    quantity: 1,
                    price: 0
                });
            },
            
            removeLine(index) {
                this.lines.splice(index, 1);
            },
            

            
            calculateTotal() {
                return this.lines.reduce((total, line) => {
                    return total + (parseFloat(line.quantity || 0) * parseFloat(line.price || 0));
                }, 0);
            },
            
            formatNumber(num) {
                return parseFloat(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        }));
    });
</script>
@endsection
