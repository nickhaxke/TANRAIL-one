@extends('layouts.management')

@section('content')
<div>
    <!-- Corporate Header -->
    <div class="bg-gray-900 rounded-t-md p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b-4 border-blue-600 mb-6">
        <div>
            <div class="text-blue-400 font-bold text-xs uppercase tracking-widest mb-1">Corporate Procurement Hub</div>
            <h1 class="text-2xl font-bold text-white">New Purchase Order</h1>
            <p class="text-gray-400 text-sm mt-1">Raise a new purchase order for authorization.</p>
        </div>
        <div class="flex gap-x-3">
            <a href="{{ route('management.procurement') }}" class="inline-flex items-center rounded bg-gray-800 px-4 py-2 text-sm font-semibold text-white border border-gray-700 hover:bg-gray-700 transition-colors">
                Back to Hub
            </a>
        </div>
    </div>

    <div class="bg-white border border-gray-300 shadow-sm rounded-b-md">
        <form method="POST" action="{{ route('management.procurement.store') }}">
            @csrf
            
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-base font-bold leading-7 text-gray-900 uppercase tracking-wide">Purchase Details</h2>
                <p class="mt-1 text-sm leading-6 text-gray-500">Provide the necessary details to generate a new PO.</p>

                <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="business_unit_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700">Business Unit</label>
                        <div class="mt-2">
                            <select id="business_unit_id" name="business_unit_id" required class="block w-full rounded border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                <option value="">Select a Business Unit</option>
                                @foreach($businessUnits as $bu)
                                    <option value="{{ $bu->id }}">{{ $bu->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="supplier_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700">Supplier</label>
                        <div class="mt-2">
                            <select id="supplier_id" name="supplier_id" required class="block w-full rounded border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                                <option value="">Select a Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="total" class="block text-xs font-bold uppercase tracking-wider text-gray-700">Total Amount (TZS)</label>
                        <div class="mt-2">
                            <input type="number" step="0.01" name="total" id="total" required class="block w-full rounded border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 flex items-center justify-end gap-x-4 bg-gray-50 border-t border-gray-300">
                <a href="{{ route('management.procurement') }}" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
                <button type="submit" class="inline-flex items-center rounded bg-blue-600 px-6 py-2 text-sm font-bold text-white uppercase tracking-wider shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors">
                    Submit for Approval
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
