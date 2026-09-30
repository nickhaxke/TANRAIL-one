@extends('layouts.management')

@section('content')
<div>
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Projects Management</h2>
            <p class="mt-1 text-sm text-gray-500">Track enterprise projects, budgets, and operational task execution.</p>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="{{ route('management.projects.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Create Project
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4 mb-6 border border-green-200">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Project Name</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Code</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Business Unit</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Budget</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($projects as $project)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                        <a href="{{ route('management.projects.show', $project) }}" class="text-indigo-600 hover:text-indigo-900">{{ $project->name }}</a>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $project->code }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $project->businessUnit->name }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        @if($project->status === 'planned')
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Planned</span>
                        @elseif($project->status === 'active')
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Active</span>
                        @elseif($project->status === 'completed')
                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">Completed</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Cancelled</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-right">{{ number_format($project->budget, 2) }}</td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <a href="{{ route('management.projects.edit', $project) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">No projects found. Create one to get started.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($projects->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $projects->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
