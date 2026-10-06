@extends('layouts.cleaning')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('cleaning.store.index') }}" class="p-2 text-slate-400 hover:text-slate-800 bg-white rounded-lg border border-slate-200 shadow-sm transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Receive Materials</h1>
            <p class="text-slate-500 text-sm mt-1">Add stock to inventory</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-100 text-green-700 rounded-xl font-medium border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-700 rounded-xl font-medium border border-red-200">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-red-100 text-red-700 rounded-xl font-medium border border-red-200">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('cleaning.store.receive.post') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Location</label>
                <select name="inventory_location_id" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-cyan-500 focus:border-cyan-500 block p-2.5 outline-none" required>
                    <option value="">Select Location</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Material</label>
                <select name="item_id" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-cyan-500 focus:border-cyan-500 block p-2.5 outline-none" required>
                    <option value="">Select Item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->sku }} - {{ $item->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Quantity</label>
                <input type="number" step="0.01" name="quantity" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-cyan-500 focus:border-cyan-500 block p-2.5 outline-none" required>
            </div>

            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl transition-colors">
                Receive Stock
            </button>
        </form>
    </div>
</div>
@endsection
