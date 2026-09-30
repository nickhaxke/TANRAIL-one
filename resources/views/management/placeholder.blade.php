@extends('layouts.management')

@section('content')
<div class="px-4 py-16 text-center sm:px-6 lg:px-8 bg-white shadow sm:rounded-lg border border-gray-100">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <h2 class="mt-2 text-base font-semibold leading-6 text-gray-900">{{ $title ?? 'Coming Soon' }}</h2>
    <p class="mt-1 text-sm text-gray-500">{{ $description ?? 'This module is currently being built and will be available soon.' }}</p>
    <div class="mt-6">
        <a href="{{ route('management.dashboard') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Back to Dashboard
        </a>
    </div>
</div>
@endsection
