@extends('layouts.restaurant')

@section('content')
<div class="max-w-7xl mx-auto w-full h-[60vh] flex items-center justify-center">
    <div class="bg-white border-2 border-gray-900 p-10 max-w-lg w-full text-center relative">
        <div class="absolute top-0 left-0 w-full h-2 bg-gray-900"></div>
        
        <div class="w-16 h-16 mx-auto mb-6 bg-gray-100 border-2 border-gray-900 flex items-center justify-center">
            <svg class="w-8 h-8 text-gray-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
        </div>
        
        <h2 class="text-2xl font-black text-gray-900 uppercase tracking-widest mb-3">{{ $title ?? 'Module Not Found' }}</h2>
        <div class="w-12 h-1 bg-gray-900 mx-auto mb-5"></div>
        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-8">
            This module is currently under construction and will be integrated in upcoming phases.
        </p>
        
        <a href="{{ route('restaurant.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold uppercase tracking-wider transition-colors border-2 border-gray-900">
            &larr; Return to Dashboard
        </a>
    </div>
</div>
@endsection