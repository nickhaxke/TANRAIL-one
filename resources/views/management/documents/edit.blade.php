@extends('layouts.management')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                {{ isset($document) ? 'Edit Document' : 'Upload Document' }}
            </h2>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <a href="{{ route('management.documents.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Back</a>
        </div>
    </div>

    <form action="{{ isset($document) ? route('management.documents.update', $document) : route('management.documents.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        @csrf
        @if(isset($document))
            @method('PUT')
        @endif
        
        <div class="px-4 py-6 sm:p-8">
            <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                
                <div class="sm:col-span-6">
                    <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Document Title</label>
                    <div class="mt-2">
                        <input type="text" name="title" id="title" value="{{ old('title', $document->title ?? '') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>
                    </div>
                    @error('title')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="document_category_id" class="block text-sm font-medium leading-6 text-gray-900">Category</label>
                    <div class="mt-2">
                        <select id="document_category_id" name="document_category_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="">-- Uncategorized --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('document_category_id', $document->document_category_id ?? '') == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('document_category_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="version" class="block text-sm font-medium leading-6 text-gray-900">Version</label>
                    <div class="mt-2">
                        <input type="text" name="version" id="version" value="{{ old('version', $document->version ?? '1.0') }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" required>
                    </div>
                    @error('version')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-6">
                    <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                    <div class="mt-2">
                        <textarea name="description" id="description" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old('description', $document->description ?? '') }}</textarea>
                    </div>
                    @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-3">
                    <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                    <div class="mt-2">
                        <select id="status" name="status" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="draft" {{ old('status', $document->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $document->status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ old('status', $document->status ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    @error('status')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div class="sm:col-span-6">
                    <label for="file" class="block text-sm font-medium leading-6 text-gray-900">File Attachment (PDF, DOCX, etc.)</label>
                    <div class="mt-2 flex items-center gap-x-3">
                        <input type="file" name="file" id="file" class="block w-full text-sm text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-600 hover:file:bg-indigo-100">
                    </div>
                    @if(isset($document) && $document->file_path)
                        <p class="mt-2 text-sm text-gray-500">Current file: <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-indigo-600 hover:underline">View</a> (Uploading a new file will replace this one)</p>
                    @endif
                    @error('file')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>
        <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8">
            <a href="{{ route('management.documents.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Save Document</button>
        </div>
    </form>
</div>
@endsection
