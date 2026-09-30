@extends('layouts.management')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                {{ isset($organization) ? 'Edit Organization' : 'Create Organization' }}
            </h2>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="{{ route('management.organization.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Back</a>
        </div>
    </div>

    <form action="{{ isset($organization) ? route('management.organization.update', $organization) : route('management.organization.store') }}" method="POST" class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        @csrf
        @if(isset($organization))
            @method('PUT')
        @endif
        
        <div class="px-4 py-6 sm:p-8">
            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <!-- Name -->
                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Organization Name</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name', $organization->name ?? '') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>
                    </div>
                    @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Code -->
                <div class="sm:col-span-6">
                    <label for="code" class="block text-sm font-medium leading-6 text-gray-900">Organization Code</label>
                    <div class="mt-2">
                        <input type="text" name="code" id="code" value="{{ old('code', $organization->code ?? '') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">A short, unique identifier for the organization (e.g., TANRAIL).</p>
                    @error('code')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Status -->
                <div class="sm:col-span-6">
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="status" name="status" type="checkbox" value="1" 
                                {{ old('status', $organization->status ?? true) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="status" class="font-medium text-gray-900">Active Status</label>
                            <p class="text-gray-500">Inactive organizations are hidden from operational processes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
            <a href="{{ route('management.organization.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save Organization</button>
        </div>
    </form>
</div>
@endsection
