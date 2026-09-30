@extends('layouts.restaurant')

@section('content')
<div x-data="kitchenApp()" class="flex-1 flex flex-col h-full bg-slate-50 overflow-hidden">
    
    <!-- Kitchen Header -->
    <div class="p-4 bg-white border-b-2 border-gray-200 flex items-center justify-between shrink-0 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <h1 class="text-base font-black text-gray-900 uppercase tracking-wider">Kitchen Display System (KDS)</h1>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 border border-yellow-600 text-[10px] font-bold bg-yellow-50 text-yellow-800 uppercase">
                    <span class="w-1.5 h-1.5 bg-yellow-600 animate-pulse"></span>
                    Live
                </span>
            </div>
            <p class="text-xs font-bold text-gray-500 uppercase">Station: <strong class="text-gray-900">{{ $branch->name }}</strong></p>
        </div>

        <div class="flex items-center gap-4">
            <!-- Active / Completed Counter Badges -->
            <div class="hidden sm:flex items-center gap-3 text-xs font-bold uppercase tracking-wider text-gray-600">
                <div class="px-3 py-1.5 border-2 border-gray-200 bg-gray-50">
                    Active: <strong class="text-gray-900 font-mono text-sm ml-1" x-text="orders.length"></strong>
                </div>
                <div class="px-3 py-1.5 border-2 border-gray-200 bg-gray-50">
                    Fulfilled: <strong class="text-gray-900 font-mono text-sm ml-1">{{ $completedCountToday }}</strong>
                </div>
            </div>

            <!-- Manual Refresh -->
            <button type="button" @click="location.reload()" class="px-4 py-2 border-2 border-gray-900 bg-white hover:bg-gray-50 text-gray-900 font-bold text-xs uppercase tracking-wider transition-colors flex items-center gap-2">
                Refresh
            </button>
        </div>
    </div>

    <!-- Active Orders Grid -->
    <div class="flex-1 overflow-y-auto p-4 sm:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-4">
            <template x-for="order in orders" :key="order.id">
                <div class="bg-white border-2 p-4 flex flex-col justify-between shadow-sm transition-all"
                     :class="getCardBorderClass(order)">
                    
                    <!-- Ticket Header -->
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b-2 border-gray-100">
                            <div>
                                <span class="font-mono text-sm font-black text-gray-900" x-text="order.order_number"></span>
                                <span class="block text-[10px] font-bold uppercase text-gray-500 mt-0.5" x-text="order.created_at + ' &bull; ' + order.cashier"></span>
                            </div>

                            <!-- Elapsed Time Badge -->
                            <span class="px-2 py-1 text-xs font-mono font-bold flex items-center gap-1 border-2"
                                  :class="getTimerBadgeClass(order.elapsed_minutes)">
                                <span x-text="order.elapsed_minutes + ' min'"></span>
                            </span>
                        </div>

                        <!-- Status Tag -->
                        <div class="py-2 mt-1">
                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 border"
                                  :class="getStatusTagClass(order.status)"
                                  x-text="order.status">
                            </span>
                        </div>

                        <!-- Ticket Items List -->
                        <div class="space-y-2 py-2">
                            <template x-for="line in order.items" :key="line.name">
                                <div class="flex items-start gap-2 bg-gray-50 p-2 border-2 border-gray-100">
                                    <span class="h-6 w-6 font-mono font-black text-xs flex items-center justify-center shrink-0 border-2 border-gray-200 bg-white text-gray-900" x-text="line.quantity + 'x'"></span>
                                    <span class="text-xs font-bold text-gray-900 uppercase leading-tight mt-1" x-text="line.name"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Kitchen Action Buttons -->
                    <div class="pt-4 border-t-2 border-gray-100 mt-2">
                        <template x-if="order.status === 'confirmed'">
                            <button type="button" 
                                    @click="updateOrderStatus(order, 'preparing')"
                                    class="w-full py-2.5 px-3 border-2 border-gray-900 bg-gray-900 hover:bg-black text-white font-bold text-xs uppercase tracking-wider transition-all">
                                Start Cooking &rarr;
                            </button>
                        </template>

                        <template x-if="order.status === 'preparing'">
                            <button type="button" 
                                    @click="updateOrderStatus(order, 'ready')"
                                    class="w-full py-2.5 px-3 border-2 border-blue-600 bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-xs uppercase tracking-wider transition-all">
                                Mark Ready &rarr;
                            </button>
                        </template>

                        <template x-if="order.status === 'ready'">
                            <button type="button" 
                                    @click="updateOrderStatus(order, 'completed')"
                                    class="w-full py-2.5 px-3 border-2 border-green-600 bg-green-600 hover:bg-green-700 text-white font-bold text-xs uppercase tracking-wider transition-all">
                                Complete &check;
                            </button>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="orders.length === 0" class="col-span-full py-24 text-center">
                <p class="text-lg font-bold text-gray-400 uppercase tracking-widest">KITCHEN IS CLEAR</p>
                <p class="text-xs font-bold text-gray-400 mt-2 uppercase">Waiting for new orders...</p>
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Kitchen Logic -->
<script>
function kitchenApp() {
    return {
        orders: @json($activeOrders),

        getCardBorderClass(order) {
            if (order.status === 'ready') return 'border-green-600 bg-green-50/30';
            if (order.status === 'preparing') return 'border-blue-600 bg-blue-50/30';
            if (order.elapsed_minutes > 15) return 'border-red-600 bg-red-50/30';
            return 'border-gray-200';
        },

        getTimerBadgeClass(mins) {
            if (mins > 20) return 'bg-red-50 text-red-800 border-red-200 animate-pulse';
            if (mins > 10) return 'bg-yellow-50 text-yellow-800 border-yellow-200';
            return 'bg-green-50 text-green-800 border-green-200';
        },

        getStatusTagClass(status) {
            if (status === 'ready') return 'bg-green-100 text-green-800 border-green-300';
            if (status === 'preparing') return 'bg-blue-100 text-blue-800 border-blue-300';
            return 'bg-yellow-100 text-yellow-800 border-yellow-300';
        },

        async updateOrderStatus(order, nextStatus) {
            try {
                const response = await fetch(`/restaurant/kitchen/${order.id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: nextStatus })
                });

                const data = await response.json();

                if (data.success) {
                    if (nextStatus === 'completed') {
                        this.orders = this.orders.filter(o => o.id !== order.id);
                    } else {
                        order.status = data.new_status;
                    }
                }
            } catch (err) {
                console.error(err);
                alert('Connection error');
            }
        }
    };
}
</script>
@endsection
