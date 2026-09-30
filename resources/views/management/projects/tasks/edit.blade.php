@extends('layouts.management')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                {{ isset($task) ? 'Edit Task' : 'Create Task' }}
            </h2>
            @if(!isset($task))
                <p class="mt-1 text-sm text-gray-500">Adding to project: {{ $project->name }}</p>
            @endif
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="{{ isset($task) ? route('management.projects.show', $task->project_id) : route('management.projects.show', $project) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Back to Project</a>
        </div>
    </div>

    <form action="{{ isset($task) ? route('management.project-tasks.update', $task) : route('management.project-tasks.store', $project) }}" method="POST" class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        @csrf
        @if(isset($task))
            @method('PUT')
        @endif
        
        <div class="px-4 py-6 sm:p-8">
            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                
                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Task Name</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name', $task->name ?? '') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>
                    </div>
                    @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div class="sm:col-span-6">
                    <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                    <div class="mt-2">
                        <textarea name="description" id="description" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old('description', $task->description ?? '') }}</textarea>
                    </div>
                    @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="assigned_to" class="block text-sm font-medium leading-6 text-gray-900">Assign To</label>
                    <div class="mt-2">
                        <select id="assigned_to" name="assigned_to" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="">-- Unassigned --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (old('assigned_to', $task->assigned_to ?? '') == $user->id) ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('assigned_to')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                    <div class="mt-2">
                        <select id="status" name="status" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="pending" {{ old('status', $task->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $task->status ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $task->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    @error('status')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div class="sm:col-span-3">
                    <label for="due_date" class="block text-sm font-medium leading-6 text-gray-900">Due Date</label>
                    <div class="mt-2">
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d') : '') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    @error('due_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>
        <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
            <a href="{{ isset($task) ? route('management.projects.show', $task->project_id) : route('management.projects.show', $project) }}" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Save Task</button>
        </div>
    </form>
</div>
@endsection
