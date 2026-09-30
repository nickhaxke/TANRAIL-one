@extends('layouts.management')

@section('content')
<div>
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">{{ $project->name }}</h2>
            <p class="mt-1 text-sm text-gray-500">Project Code: {{ $project->code }} | Business Unit: {{ $project->businessUnit->name }}</p>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0 gap-x-3">
            <a href="{{ route('management.projects.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Back to Projects</a>
            <a href="{{ route('management.project-tasks.create', $project) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Add Task</a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <div class="overflow-hidden bg-white shadow sm:rounded-lg mb-8 border border-gray-200">
        <div class="px-4 py-6 sm:px-6">
            <h3 class="text-base font-semibold leading-7 text-gray-900">Project Information</h3>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">{{ $project->description ?? 'No description provided.' }}</p>
        </div>
        <div class="border-t border-gray-100">
            <dl class="divide-y divide-gray-100">
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-900">Status</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 uppercase">{{ $project->status }}</span>
                    </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-900">Budget</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ number_format($project->budget, 2) }}</dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-900">Timeline</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ $project->start_date ? $project->start_date->format('M d, Y') : 'TBD' }} 
                        - 
                        {{ $project->end_date ? $project->end_date->format('M d, Y') : 'TBD' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Tasks -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
        <div class="px-4 py-5 sm:px-6 bg-slate-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold leading-6 text-gray-900">Tasks</h3>
        </div>
        <ul role="list" class="divide-y divide-gray-100">
            @forelse($project->tasks as $task)
            <li class="flex items-center justify-between gap-x-6 py-5 px-4 sm:px-6 hover:bg-gray-50">
                <div class="min-w-0 flex-1">
                    <div class="flex items-start gap-x-3">
                        <p class="text-sm font-semibold leading-6 text-gray-900">{{ $task->name }}</p>
                        @if($task->status === 'pending')
                            <p class="rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset text-yellow-700 bg-yellow-50 ring-yellow-600/20">Pending</p>
                        @elseif($task->status === 'in_progress')
                            <p class="rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset text-blue-700 bg-blue-50 ring-blue-600/20">In Progress</p>
                        @else
                            <p class="rounded-md whitespace-nowrap mt-0.5 px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset text-green-700 bg-green-50 ring-green-600/20">Completed</p>
                        @endif
                    </div>
                    <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                        <p class="truncate">{{ $task->description }}</p>
                    </div>
                    <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500">
                        <p class="whitespace-nowrap">Due: {{ $task->due_date ? $task->due_date->format('M d, Y') : 'None' }}</p>
                        <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current"><circle cx="1" cy="1" r="1" /></svg>
                        <p class="truncate">Assignee: {{ $task->assignee ? $task->assignee->name : 'Unassigned' }}</p>
                    </div>
                </div>
                <div class="flex flex-none items-center gap-x-4">
                    <a href="{{ route('management.project-tasks.edit', $task) }}" class="hidden rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block">Edit Task</a>
                    
                    <form action="{{ route('management.project-tasks.destroy', $task) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this task?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </div>
            </li>
            @empty
            <li class="px-4 py-8 text-center text-sm text-gray-500">No tasks have been added to this project yet.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
