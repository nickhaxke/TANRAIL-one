@extends('layouts.management')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-[#0A1A2F] sm:truncate sm:text-3xl sm:tracking-tight">
                {{ isset($location) ? 'Edit Location' : 'Add Location' }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ isset($location) ? 'Update location details.' : 'Create a new organizational physical location.' }}
            </p>
        </div>
        <div>
            <a href="{{ route('management.organization.locations') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Back to Locations
            </a>
        </div>
    </div>
</div>

<div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6">
    <form action="{{ isset($location) ? route('management.locations.update', $location) : route('management.locations.store') }}" method="POST">
        @csrf
        @if(isset($location))
            @method('PUT')
        @endif

        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Location Name</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name', $location->name ?? '') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                    @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="branch_id" class="block text-sm font-medium leading-6 text-gray-900">Linked Branch (Optional)</label>
                    <div class="mt-2">
                        <select id="branch_id" name="branch_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                            <option value="">No linked branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $location->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('branch_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium leading-6 text-gray-900">Address</label>
                    <div class="mt-2">
                        <input type="text" name="address" id="address" value="{{ old('address', $location->address ?? '') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                    @error('address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="relative flex items-start">
                <div class="flex h-6 items-center">
                    <input id="status" name="status" type="checkbox" value="1" {{ old('status', $location->status ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                </div>
                <div class="ml-3 text-sm leading-6">
                    <label for="status" class="font-medium text-gray-900">Active Status</label>
                    <p class="text-gray-500">Is this location currently active?</p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end gap-x-6 border-t border-gray-900/10 pt-6">
            <a href="{{ route('management.organization.locations') }}" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
            <button type="submit" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                {{ isset($location) ? 'Update Location' : 'Save Location' }}
            </button>
        </div>
    </form>
</div>
@endsection
