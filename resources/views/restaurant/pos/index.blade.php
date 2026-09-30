@extends('layouts.restaurant')

@section('content')
<div x-data="posApp()" class="flex-1 flex flex-col lg:flex-row h-full overflow-hidden bg-slate-50">
    
    <!-- Left Section: Menu Catalog & Categories -->
    <div class="flex-1 flex flex-col min-w-0 bg-white border-r-2 border-gray-200 overflow-hidden">
        
        <!-- Active Shift Header Badge -->
        <div class="px-4 py-3 border-b-2 border-gray-200 flex flex-wrap items-center justify-between text-xs font-bold uppercase tracking-wider text-gray-700 bg-gray-50">
            <div class="flex items-center gap-4">
                @if(isset($activeShift) && $activeShift)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-green-100 text-green-800 border border-green-300">
                        <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                        Shift #{{ $activeShift->id }} Active
                    </span>
                    <span class="hidden sm:inline">Float: <strong class="text-gray-900 font-mono">TZS {{ number_format($activeShift->opening_float, 0) }}</strong></span>
                    <span class="hidden sm:inline text-gray-400">|</span>
                    <span class="hidden sm:inline">Sales: <strong class="text-green-700 font-mono">+TZS {{ number_format($activeShift->total_sales, 0) }}</strong></span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-yellow-100 text-yellow-800 border border-yellow-300">
                        ⚠️ No Shift Active
                    </span>
                    <a href="{{ route('restaurant.shifts') }}" class="text-blue-600 hover:underline">Open Shift &rarr;</a>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('restaurant.shifts') }}" class="text-gray-500 hover:text-gray-900 flex items-center gap-1">
                    Shift Drawer
                </a>
                <a href="{{ route('restaurant.expenses') }}" class="text-gray-500 hover:text-gray-900 flex items-center gap-1">
                    Expenses
                </a>
            </div>
        </div>

        <!-- Controls: Category Pills & Live Search Bar -->
        <div class="p-4 border-b-2 border-gray-200 flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between bg-white shrink-0">
            
            <!-- Category Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                <template x-for="cat in categories" :key="cat.id">
                    <button type="button" 
                            @click="selectedCategory = cat.id"
                            :class="selectedCategory === cat.id ? 'bg-gray-900 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border-gray-200'"
                            class="px-4 py-2 border-2 text-xs font-bold uppercase tracking-wider whitespace-nowrap transition-colors flex items-center gap-1.5">
                        <span x-text="cat.name"></span>
                        <span class="opacity-70" x-text="'(' + countByCategory(cat.id) + ')'"></span>
                    </button>
                </template>
            </div>

            <!-- Search Field -->
            <div class="relative sm:w-72 shrink-0">
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Search menu..." 
                       class="w-full bg-white border-2 border-gray-300 pl-10 pr-4 py-2 text-sm font-bold text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-900 focus:ring-0">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Product Cards Grid -->
        <div class="flex-1 overflow-y-auto p-5 bg-slate-50">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                <template x-for="item in filteredItems" :key="item.id">
                    <button type="button" 
                            @click="addToCart(item)"
                            :disabled="item.track_inventory && item.stock <= 0"
                            :class="item.track_inventory && item.stock <= 0 ? 'opacity-50 cursor-not-allowed bg-gray-100 border-gray-200' : 'bg-white border-gray-300 hover:border-gray-900 active:bg-gray-50'"
                            class="group text-left border-2 p-4 flex flex-col justify-between h-48 relative transition-colors shadow-sm">
                        
                        <!-- Top details -->
                        <div>
                            <div class="flex items-start justify-between gap-1 mb-2">
                                <span class="font-bold text-[10px] text-gray-500 uppercase tracking-wider bg-gray-100 px-2 py-0.5 border border-gray-200" x-text="item.category"></span>
                                
                                <!-- Stock status -->
                                <template x-if="!item.track_inventory">
                                    <span class="text-[10px] font-bold text-gray-400">UNLIMITED</span>
                                </template>
                                <template x-if="item.track_inventory && item.stock > 10">
                                    <span class="text-[10px] font-bold text-green-700 bg-green-50 px-2 py-0.5 border border-green-200" x-text="item.stock + ' LEFT'"></span>
                                </template>
                                <template x-if="item.track_inventory && item.stock > 0 && item.stock <= 10">
                                    <span class="text-[10px] font-bold text-orange-700 bg-orange-50 px-2 py-0.5 border border-orange-200" x-text="'LOW: ' + item.stock"></span>
                                </template>
                                <template x-if="item.track_inventory && item.stock <= 0">
                                    <span class="text-[10px] font-bold text-red-700 bg-red-50 px-2 py-0.5 border border-red-200">SOLD OUT</span>
                                </template>
                            </div>
                            <h3 class="font-black text-sm text-gray-900 leading-tight line-clamp-2 mt-2" x-text="item.name"></h3>
                        </div>

                        <!-- Price & Add Button -->
                        <div class="flex items-end justify-between mt-4">
                            <div>
                                <span class="text-[10px] font-bold text-gray-500 block leading-none mb-1">TZS</span>
                                <span class="text-base font-black text-gray-900 leading-none" x-text="formatCurrency(item.price)"></span>
                            </div>
                            <div :class="item.track_inventory && item.stock <= 0 ? 'bg-gray-200 text-gray-400' : 'bg-gray-900 text-white'"
                                 class="h-8 w-8 flex items-center justify-center font-bold text-lg shadow-sm">
                                <span x-text="item.track_inventory && item.stock <= 0 ? '✕' : '+'"></span>
                            </div>
                        </div>
                    </button>
                </template>

                <!-- Empty State -->
                <div x-show="filteredItems.length === 0" class="col-span-full py-16 text-center">
                    <p class="text-lg font-bold text-gray-400 uppercase tracking-widest">NO ITEMS FOUND</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Section: Station Cart & Checkout Terminal -->
    <div class="w-full lg:w-96 xl:w-[420px] bg-white flex flex-col shrink-0 lg:border-l-2 border-gray-200 h-[50vh] lg:h-full">
        
        <!-- Cart Header & Order Parameters -->
        <div class="p-5 border-b-2 border-gray-200 bg-gray-50 shrink-0">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                        <span>Current Order</span>
                        <span x-show="cart.length > 0" x-cloak class="px-2 py-0.5 text-[10px] font-bold bg-gray-900 text-white" x-text="cart.length"></span>
                    </h2>
                    <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider font-bold">Terminal: <span class="text-gray-900">{{ $branch->name }}</span></p>
                </div>
                <button type="button" @click="clearCart()" x-show="cart.length > 0" x-cloak class="text-xs font-bold text-red-600 hover:underline uppercase tracking-wider">
                    Clear
                </button>
            </div>

            <!-- Dining Type Control -->
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button type="button" 
                        @click="diningType = 'dine_in'"
                        :class="diningType === 'dine_in' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-100'"
                        class="py-2 px-1 text-[11px] font-bold uppercase tracking-wider border-2 text-center transition-colors">
                    Dine-In
                </button>
                <button type="button" 
                        @click="diningType = 'takeaway'"
                        :class="diningType === 'takeaway' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-100'"
                        class="py-2 px-1 text-[11px] font-bold uppercase tracking-wider border-2 text-center transition-colors">
                    Takeaway
                </button>
                <button type="button" 
                        @click="diningType = 'train_delivery'"
                        :class="diningType === 'train_delivery' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-100'"
                        class="py-2 px-1 text-[11px] font-bold uppercase tracking-wider border-2 text-center transition-colors">
                    Train Seat
                </button>
            </div>

            <!-- Table Input -->
            <div :class="diningType === 'takeaway' ? 'opacity-50' : 'opacity-100'" class="transition-opacity">
                <input type="text" 
                       x-model="tableNumber" 
                       :disabled="diningType === 'takeaway'"
                       :placeholder="diningType === 'train_delivery' ? 'COACH & SEAT (e.g. C4-S22)' : 'TABLE NUMBER (OPTIONAL)'" 
                       class="w-full bg-white border-2 border-gray-300 px-4 py-2 text-xs font-bold text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-900 disabled:bg-gray-100">
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-white">
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="p-3 border-2 border-gray-200 bg-white">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-xs font-bold text-gray-900 pr-4" x-text="item.name"></h4>
                        <button type="button" @click="removeFromCart(index)" class="text-gray-400 hover:text-red-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <!-- Qty Stepper -->
                        <div class="flex items-center border-2 border-gray-200 bg-gray-50">
                            <button type="button" @click="decrementQty(index)" class="w-8 h-8 font-bold text-gray-600 hover:bg-gray-200 flex items-center justify-center">-</button>
                            <span class="w-10 text-center text-xs font-black text-gray-900" x-text="item.quantity"></span>
                            <button type="button" @click="incrementQty(index)" class="w-8 h-8 font-bold text-gray-600 hover:bg-gray-200 flex items-center justify-center">+</button>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] text-gray-500 font-bold mb-0.5">TZS <span x-text="formatCurrency(item.price)"></span> EA</div>
                            <div class="text-sm font-black text-gray-900 font-mono" x-text="formatCurrency(item.price * item.quantity)"></div>
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="cart.length === 0" x-cloak class="h-full flex flex-col items-center justify-center text-center">
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">CART IS EMPTY</p>
            </div>
        </div>

        <!-- Checkout Summary -->
        <div class="p-5 border-t-2 border-gray-900 bg-gray-50 shrink-0">
            <div class="space-y-2 text-xs font-bold text-gray-500 mb-4">
                <div class="flex justify-between items-center">
                    <span class="uppercase tracking-wider">Subtotal</span>
                    <span class="text-gray-900">TZS <span x-text="formatCurrency(subtotal)"></span></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="uppercase tracking-wider">VAT (18% Inc.)</span>
                    <span class="text-gray-900">TZS <span x-text="formatCurrency(taxTotal)"></span></span>
                </div>
            </div>
            
            <div class="flex justify-between items-end mb-5">
                <span class="text-sm font-black text-gray-900 uppercase tracking-wider">Total</span>
                <span class="text-2xl font-black text-gray-900 font-mono leading-none">TZS <span x-text="formatCurrency(grandTotal)"></span></span>
            </div>

            <!-- Pay Now Button -->
            <button type="button" 
                    @click="openPaymentModal()"
                    :disabled="cart.length === 0"
                    :class="cart.length > 0 ? 'bg-gray-900 hover:bg-black text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                    class="w-full py-4 border-2 border-transparent font-black text-sm uppercase tracking-widest transition-colors flex items-center justify-center gap-2">
                Checkout Order &rarr;
            </button>
        </div>
    </div>

    <!-- Modal 1: Payment Processing -->
    <div x-show="paymentModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
        
        <div @click.outside="paymentModalOpen = false" 
             class="bg-white border-2 border-gray-900 w-full max-w-lg p-8 space-y-6 shadow-2xl">
            
            <div class="flex items-center justify-between pb-4 border-b-2 border-gray-200">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">Payment Terminal</span>
                    <h3 class="text-xl font-black text-gray-900 uppercase">Complete Payment</h3>
                </div>
                <button type="button" @click="paymentModalOpen = false" class="text-gray-400 hover:text-gray-900 text-xl font-bold">&times;</button>
            </div>

            <!-- Amount Due Banner -->
            <div class="bg-gray-50 border-2 border-gray-900 p-6 text-center">
                <span class="text-xs font-bold text-gray-600 uppercase tracking-wider block">Total Amount Due</span>
                <span class="text-4xl font-black text-gray-900 font-mono mt-2 block">TZS <span x-text="formatCurrency(grandTotal)"></span></span>
            </div>

            <!-- Payment Method Selector -->
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Select Payment Method</label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" 
                            @click="paymentMethod = 'cash'; amountPaid = grandTotal"
                            :class="paymentMethod === 'cash' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'"
                            class="p-4 border-2 text-xs font-bold uppercase tracking-wider transition-colors">
                        Cash
                    </button>
                    <button type="button" 
                            @click="paymentMethod = 'mobile_money'; amountPaid = grandTotal"
                            :class="paymentMethod === 'mobile_money' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'"
                            class="p-4 border-2 text-xs font-bold uppercase tracking-wider transition-colors">
                        M-Pesa
                    </button>
                    <button type="button" 
                            @click="paymentMethod = 'card'; amountPaid = grandTotal"
                            :class="paymentMethod === 'card' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'"
                            class="p-4 border-2 text-xs font-bold uppercase tracking-wider transition-colors">
                        Card
                    </button>
                </div>
            </div>

            <!-- Cash Input -->
            <div x-show="paymentMethod === 'cash'" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Cash Tendered (TZS)</label>
                    <input type="number" 
                           x-model.number="amountPaid" 
                           class="w-full bg-white border-2 border-gray-300 px-4 py-3 text-lg font-mono font-bold text-gray-900 focus:outline-none focus:border-gray-900">
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="amountPaid = grandTotal" class="px-3 py-1 border border-gray-300 text-xs font-bold uppercase tracking-wider hover:bg-gray-100">Exact</button>
                    <button type="button" @click="amountPaid = Math.ceil(grandTotal / 5000) * 5000" class="px-3 py-1 border border-gray-300 text-xs font-bold uppercase tracking-wider hover:bg-gray-100">Round 5K</button>
                    <button type="button" @click="amountPaid = Math.ceil(grandTotal / 10000) * 10000" class="px-3 py-1 border border-gray-300 text-xs font-bold uppercase tracking-wider hover:bg-gray-100">Round 10K</button>
                </div>
                <div class="p-4 bg-gray-50 border-2 border-gray-200 flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Change Due:</span>
                    <span class="text-xl font-black font-mono" :class="amountPaid >= grandTotal ? 'text-green-700' : 'text-red-600'" x-text="'TZS ' + formatCurrency(Math.max(0, amountPaid - grandTotal))"></span>
                </div>
            </div>

            <div x-show="paymentMethod !== 'cash'">
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Reference / Auth Code</label>
                <input type="text" 
                       x-model="paymentReference" 
                       class="w-full bg-white border-2 border-gray-300 px-4 py-3 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900">
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-4 pt-4 border-t-2 border-gray-200">
                <button type="button" 
                        @click="paymentModalOpen = false" 
                        class="w-1/3 py-3 border-2 border-gray-300 bg-white hover:bg-gray-50 font-bold text-xs uppercase tracking-wider text-gray-700">
                    Cancel
                </button>
                <button type="button" 
                        @click="submitOrder()" 
                        :disabled="isSubmitting || (paymentMethod === 'cash' && amountPaid < grandTotal)"
                        class="w-2/3 py-3 border-2 border-gray-900 bg-gray-900 hover:bg-black text-white font-bold text-xs uppercase tracking-widest disabled:opacity-50 disabled:bg-gray-300 disabled:border-gray-300 disabled:text-gray-500 flex items-center justify-center">
                    <span x-show="!isSubmitting">Confirm & Print &rarr;</span>
                    <span x-show="isSubmitting">Processing...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 2: Official Receipt -->
    <div x-show="receiptModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
        
        <div @click.outside="receiptModalOpen = false" 
             class="bg-white border border-gray-300 shadow-xl p-8 w-full max-w-sm text-gray-900 font-sans text-xs">
            
            <div class="text-center pb-4 border-b-2 border-gray-900 mb-4">
                <h2 class="font-black text-base uppercase tracking-widest">TANRAIL INVESTMENTS</h2>
                <p class="font-bold text-gray-600 mt-1" x-text="receiptData?.station_name"></p>
                <p class="text-[10px] text-gray-500 mt-1">TIN: 108-342-880 &bull; VRN: 40-001928-Z</p>
                <div class="mt-3 font-mono font-bold text-sm" x-text="receiptData?.receipt_number"></div>
            </div>

            <div class="space-y-1 text-[11px] font-bold text-gray-600 border-b border-gray-300 pb-3 mb-3">
                <div class="flex justify-between">
                    <span>DATE:</span>
                    <span class="text-gray-900" x-text="receiptData?.timestamp"></span>
                </div>
                <div class="flex justify-between">
                    <span>CASHIER:</span>
                    <span class="text-gray-900 uppercase" x-text="receiptData?.cashier_name"></span>
                </div>
                <div class="flex justify-between">
                    <span>TYPE:</span>
                    <span class="text-gray-900 uppercase" x-text="receiptData?.dining_type + ' (' + receiptData?.table_number + ')'"></span>
                </div>
            </div>

            <div class="space-y-2 border-b-2 border-gray-900 pb-3 mb-3">
                <template x-for="item in receiptData?.items || []" :key="item.name">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 pr-2">
                            <span class="font-bold text-gray-900 uppercase block" x-text="item.name"></span>
                            <span class="text-[10px] font-bold text-gray-500 font-mono" x-text="item.qty + ' x TZS ' + item.price"></span>
                        </div>
                        <span class="font-bold font-mono text-gray-900" x-text="item.total"></span>
                    </div>
                </template>
            </div>

            <div class="space-y-1 font-bold text-gray-600 text-[11px] mb-4">
                <div class="flex justify-between">
                    <span>SUBTOTAL:</span>
                    <span class="font-mono text-gray-900" x-text="receiptData?.subtotal"></span>
                </div>
                <div class="flex justify-between">
                    <span>VAT (18% INC):</span>
                    <span class="font-mono text-gray-900" x-text="receiptData?.tax_total"></span>
                </div>
                <div class="flex justify-between text-sm font-black text-gray-900 pt-2 pb-2 mt-2 border-y border-gray-300">
                    <span>TOTAL:</span>
                    <span class="font-mono">TZS <span x-text="receiptData?.total"></span></span>
                </div>
                <div class="flex justify-between mt-2">
                    <span x-text="'PAID (' + receiptData?.payment_method + '):'"></span>
                    <span class="font-mono text-gray-900" x-text="receiptData?.amount_paid"></span>
                </div>
                <div class="flex justify-between">
                    <span>CHANGE:</span>
                    <span class="font-mono text-gray-900" x-text="receiptData?.change"></span>
                </div>
            </div>

            <div class="text-center pt-2 space-y-4">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Thank you for dining with us</p>
                <div class="flex flex-col gap-2">
                    <button type="button" @click="window.print()" class="w-full py-2 border-2 border-gray-900 bg-gray-900 text-white font-bold text-xs uppercase tracking-widest hover:bg-black">
                        Print
                    </button>
                    <button type="button" @click="receiptModalOpen = false" class="w-full py-2 border-2 border-gray-300 bg-white text-gray-700 font-bold text-xs uppercase tracking-widest hover:bg-gray-50">
                        New Order
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Alpine.js POS App Logic -->
<script>
function posApp() {
    return {
        categories: [
            { id: 'all', name: 'All Items' },
            @foreach($categories as $cat)
            { id: '{{ $cat->name }}', name: '{{ $cat->name }}' },
            @endforeach
        ],
        selectedCategory: 'all',
        searchQuery: '',
        allItems: @json($items),
        cart: [],
        diningType: 'dine_in',
        tableNumber: '',
        paymentModalOpen: false,
        paymentMethod: 'cash',
        amountPaid: 0,
        paymentReference: '',
        isSubmitting: false,
        receiptModalOpen: false,
        receiptData: null,
        branchId: {{ $branch->id }},

        get filteredItems() {
            return this.allItems.filter(item => {
                const matchesCategory = this.selectedCategory === 'all' || item.category === this.selectedCategory;
                const matchesSearch = !this.searchQuery || 
                    item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                    item.sku.toLowerCase().includes(this.searchQuery.toLowerCase());
                return matchesCategory && matchesSearch;
            });
        },

        countByCategory(catId) {
            if (catId === 'all') return this.allItems.length;
            return this.allItems.filter(i => i.category === catId).length;
        },

        addToCart(item) {
            if (item.track_inventory && item.stock <= 0) return;
            const existingIndex = this.cart.findIndex(i => i.id === item.id);
            if (existingIndex > -1) {
                if (item.track_inventory && this.cart[existingIndex].quantity >= item.stock) return;
                this.cart[existingIndex].quantity += 1;
            } else {
                this.cart.push({ id: item.id, name: item.name, price: item.price, stock: item.stock, track_inventory: item.track_inventory, quantity: 1 });
            }
        },

        incrementQty(index) {
            const item = this.cart[index];
            if (item.track_inventory && item.quantity >= item.stock) return;
            this.cart[index].quantity += 1;
        },

        decrementQty(index) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity -= 1;
            } else {
                this.cart.splice(index, 1);
            }
        },

        removeFromCart(index) { this.cart.splice(index, 1); },
        clearCart() { this.cart = []; },

        get subtotal() { return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0); },
        get taxTotal() { return Math.round(this.subtotal * 0.18 * 100) / 100; },
        get grandTotal() { return this.subtotal + this.taxTotal; },

        openPaymentModal() {
            if (this.cart.length === 0) return;
            this.amountPaid = this.grandTotal;
            this.paymentModalOpen = true;
        },

        async submitOrder() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;

            const payload = {
                branch_id: this.branchId,
                dining_type: this.diningType,
                table_number: this.tableNumber,
                payment_method: this.paymentMethod,
                amount_paid: this.amountPaid,
                payment_reference: this.paymentReference,
                items: this.cart.map(i => ({ id: i.id, quantity: i.quantity }))
            };

            try {
                const response = await fetch("{{ route('restaurant.pos.order') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (data.success) {
                    this.receiptData = data.receipt;
                    this.paymentModalOpen = false;
                    this.receiptModalOpen = true;
                    this.cart = [];
                    this.tableNumber = '';
                    this.paymentReference = '';
                    if (data.updated_stock) {
                        for (const [id, newQty] of Object.entries(data.updated_stock)) {
                            const found = this.allItems.find(i => i.id == id);
                            if (found) found.stock = Number(newQty);
                        }
                    }
                } else {
                    alert(data.message || 'Error processing order');
                }
            } catch (err) {
                console.error(err);
                alert('Connection error');
            } finally {
                this.isSubmitting = false;
            }
        },

        formatCurrency(val) {
            return Number(val || 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }
    };
}
</script>
@endsection
