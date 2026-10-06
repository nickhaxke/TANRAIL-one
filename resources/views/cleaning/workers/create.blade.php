@extends('layouts.cleaning')

@section('content')
<div class="max-w-3xl mx-auto w-full">

    <div class="mb-8 border-b-2 border-gray-900 pb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="text-xs font-bold tracking-widest text-gray-500 uppercase mb-1">Personnel Management</div>
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">Add Cleaning Worker</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('cleaning.workers.index') }}" class="text-sm font-bold text-gray-500 hover:text-gray-900 uppercase tracking-wider transition-colors">
                &larr; Back to List
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-600 p-4 mb-6">
            <ul class="list-disc pl-5 text-sm font-bold text-red-900">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border-2 border-gray-200 p-6">
        <form action="{{ route('cleaning.workers.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="worker_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Worker ID (Internal)</label>
                <input type="text" name="worker_id" id="worker_id" value="{{ old('worker_id') }}" class="w-full border-2 border-gray-300 p-2 text-sm font-medium focus:border-gray-900 focus:ring-0" required placeholder="e.g. CW-001">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="w-full border-2 border-gray-300 p-2 text-sm font-medium focus:border-gray-900 focus:ring-0" required>
                </div>
                <div>
                    <label for="last_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="w-full border-2 border-gray-300 p-2 text-sm font-medium focus:border-gray-900 focus:ring-0" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="id_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">NIDA / ID Number</label>
                    <input type="text" name="id_number" id="id_number" value="{{ old('id_number') }}" class="w-full border-2 border-gray-300 p-2 text-sm font-medium focus:border-gray-900 focus:ring-0">
                </div>
                <div>
                    <label for="phone_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" class="w-full border-2 border-gray-300 p-2 text-sm font-medium focus:border-gray-900 focus:ring-0">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="current_branch_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Station / Branch Assignment</label>
                    <select name="current_branch_id" id="current_branch_id" class="w-full border-2 border-gray-300 p-2 text-sm font-medium focus:border-gray-900 focus:ring-0">
                        <option value="">-- Unassigned --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('current_branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="current_supervisor_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Responsible Supervisor</label>
                    <select name="current_supervisor_id" id="current_supervisor_id" class="w-full border-2 border-gray-300 p-2 text-sm font-medium focus:border-gray-900 focus:ring-0">
                        <option value="">-- None --</option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}" {{ old('current_supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                {{ $supervisor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-4 flex items-center gap-3 border-t-2 border-gray-100">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="h-5 w-5 text-gray-900 border-2 border-gray-300 focus:ring-gray-900" checked>
                <label for="is_active" class="text-sm font-bold text-gray-900 uppercase tracking-wider">Active Worker</label>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full px-5 py-3 border-2 border-gray-900 bg-gray-900 text-white font-bold text-sm uppercase tracking-wider hover:bg-gray-800 transition-colors">
                    Save Worker
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
