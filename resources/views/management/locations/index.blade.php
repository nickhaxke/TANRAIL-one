@extends('layouts.management')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-[#0A1A2F] sm:truncate sm:text-3xl sm:tracking-tight">Locations</h2>
            <p class="mt-1 text-sm text-gray-500">Manage organizational physical locations.</p>
        </div>
        <div>
            <a href="{{ route('management.locations.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Location
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
</div>
@endif

<div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">LOCATION NAME</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">ADDRESS</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">LINKED BRANCH</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">STATUS</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($locations as $location)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                        {{ $location->name }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        {{ $location->address }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        {{ $location->branch ? $location->branch->name : 'N/A' }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        @if($location->status)
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">Inactive</span>
                        @endif
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <a href="{{ route('management.locations.edit', $location) }}" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                        
                        <form action="{{ route('management.locations.destroy', $location) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this location?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-sm text-gray-500">
                        No locations found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
