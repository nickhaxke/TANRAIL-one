@extends('layouts.management')

@section('content')
<div>
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Document Center</h2>
            <p class="mt-1 text-sm text-gray-500">Manage corporate policies, compliance documents, and operational files.</p>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="{{ route('management.documents.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Upload Document
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
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Title</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Category</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Version</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Uploaded</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($documents as $doc)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                        @if($doc->file_path)
                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center gap-x-2">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                {{ $doc->title }}
                            </a>
                        @else
                            {{ $doc->title }}
                        @endif
                        <p class="text-xs text-gray-500 font-normal mt-0.5 truncate max-w-xs">{{ $doc->description }}</p>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $doc->category->name ?? 'Uncategorized' }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">v{{ $doc->version }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        @if($doc->status === 'draft')
                            <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Draft</span>
                        @elseif($doc->status === 'published')
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Published</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">Archived</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $doc->created_at->format('M d, Y') }}</td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <a href="{{ route('management.documents.edit', $doc) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                        <form action="{{ route('management.documents.destroy', $doc) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this document?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">No documents found. Upload one to get started.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($documents->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $documents->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
