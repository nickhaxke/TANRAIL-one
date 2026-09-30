@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full" x-data="{ showAddModal: false }">
    <!-- Header -->
    <div class="mb-8 border-b border-gray-200 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-bold tracking-widest text-gray-500 uppercase">Procurement Module</span>
                <span class="text-xs font-bold text-gray-400">|</span>
                <span class="text-xs font-bold text-gray-900 uppercase">Suppliers Directory</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Manage Suppliers</h1>
        </div>
        
        <div class="flex items-center gap-2">
            <button @click="showAddModal = true" class="px-5 py-2.5 bg-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors shadow-sm rounded-sm">
                + Add Supplier
            </button>
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

    <!-- Suppliers List -->
    <div class="bg-white border border-gray-200 rounded-sm shadow-sm overflow-hidden mb-8">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="py-3 px-4 font-bold text-gray-600 uppercase text-[10px] tracking-wider">Supplier Name</th>
                    <th class="py-3 px-4 font-bold text-gray-600 uppercase text-[10px] tracking-wider">Contact Person</th>
                    <th class="py-3 px-4 font-bold text-gray-600 uppercase text-[10px] tracking-wider">TIN Number</th>
                    <th class="py-3 px-4 font-bold text-gray-600 uppercase text-[10px] tracking-wider">Phone</th>
                    <th class="py-3 px-4 font-bold text-gray-600 uppercase text-[10px] tracking-wider text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($suppliers as $sup)
                @php $details = is_string($sup->contact_details) ? json_decode($sup->contact_details, true) : ($sup->contact_details ?? []); @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-4 font-bold text-gray-900">{{ $sup->name }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $details['contact_person'] ?? '-' }}</td>
                    <td class="py-3 px-4 font-mono text-gray-700">{{ $sup->tax_number ?? '-' }}</td>
                    <td class="py-3 px-4 font-mono text-gray-700">{{ $details['phone'] ?? '-' }}</td>
                    <td class="py-3 px-4 text-center">
                        @if($sup->status)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-wider">Active</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-red-100 text-red-800 uppercase tracking-wider">Inactive</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 px-4 text-center text-gray-500 font-medium">
                        No suppliers registered yet. Click "+ Add Supplier" to register one.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Add Supplier Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showAddModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>

            <div x-show="showAddModal" @click.away="showAddModal = false" class="relative z-10 inline-block align-bottom bg-white rounded-sm text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('restaurant.purchasing.suppliers.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-xl leading-6 font-black text-gray-900 tracking-tight uppercase mb-4" id="modal-title">
                                    Register New Supplier
                                </h3>
                                <div class="mt-2 space-y-4">
                                    
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Company / Supplier Name <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" class="w-full bg-gray-50 border border-gray-300 rounded-sm px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 focus:bg-white shadow-sm" required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Contact Person</label>
                                        <input type="text" name="contact_person" class="w-full bg-gray-50 border border-gray-300 rounded-sm px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 focus:bg-white shadow-sm">
                                    </div>
                                    
                                    <div class="flex gap-4">
                                        <div class="w-1/2">
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Phone</label>
                                            <input type="text" name="phone" class="w-full bg-gray-50 border border-gray-300 rounded-sm px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 focus:bg-white shadow-sm font-mono">
                                        </div>
                                        <div class="w-1/2">
                                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Email</label>
                                            <input type="email" name="email" class="w-full bg-gray-50 border border-gray-300 rounded-sm px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 focus:bg-white shadow-sm">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Physical Address</label>
                                        <input type="text" name="address" class="w-full bg-gray-50 border border-gray-300 rounded-sm px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 focus:bg-white shadow-sm">
                                    </div>

                                    <!-- Tax & Financial Info -->
                                    <div class="pt-2 mt-2 border-t border-gray-100">
                                        <h4 class="text-[10px] font-black text-gray-900 tracking-widest uppercase mb-3">Financial & Tax Details</h4>
                                        <div class="flex gap-4 mb-4">
                                            <div class="w-full">
                                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">TIN Number</label>
                                                <input type="text" name="tax_number" placeholder="e.g. 100-200-300" class="w-full bg-gray-50 border border-gray-300 rounded-sm px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 focus:bg-white shadow-sm font-mono">
                                            </div>
                                        </div>
                                        <div class="flex gap-4">
                                            <div class="w-1/2">
                                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Bank Name</label>
                                                <input type="text" name="bank_name" placeholder="e.g. CRDB Bank" class="w-full bg-gray-50 border border-gray-300 rounded-sm px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 focus:bg-white shadow-sm">
                                            </div>
                                            <div class="w-1/2">
                                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Account Number</label>
                                                <input type="text" name="account_number" class="w-full bg-gray-50 border border-gray-300 rounded-sm px-3 py-2 text-sm font-bold text-gray-900 focus:outline-none focus:border-gray-900 focus:bg-white shadow-sm font-mono">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                        <button type="submit" class="w-full inline-flex justify-center rounded-sm border border-transparent shadow-sm px-4 py-2 bg-gray-900 text-base font-bold text-white hover:bg-gray-800 uppercase tracking-wider text-sm focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Save Supplier
                        </button>
                        <button type="button" @click="showAddModal = false" class="mt-3 w-full inline-flex justify-center rounded-sm border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 uppercase tracking-wider text-sm focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
