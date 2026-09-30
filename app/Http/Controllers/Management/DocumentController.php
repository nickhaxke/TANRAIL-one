<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Document;
use App\Domains\Core\Models\DocumentCategory;
use App\Domains\Core\Models\Organization;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with('category')->orderBy('created_at', 'desc')->paginate(15);

        return view('management.documents.index', compact('documents'));
    }

    public function create()
    {
        $categories = DocumentCategory::all();

        return view('management.documents.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_category_id' => 'nullable|exists:document_categories,id',
            'version' => 'required|string|max:20',
            'status' => 'required|in:draft,published,archived',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('documents', 'public');
            $validated['file_path'] = $path;
        }

        // Default to current user's organization context via ContextManager or assume global for now
        // But organization_id is required in the DB, so we should inject the current organization
        // For HQ, we might get the first organization or the one from context
        $validated['organization_id'] = Organization::first()->id;

        Document::create($validated);

        return redirect()->route('management.documents.index')->with('success', 'Document uploaded successfully.');
    }

    public function edit(Document $document)
    {
        $categories = DocumentCategory::all();

        return view('management.documents.edit', compact('document', 'categories'));
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_category_id' => 'nullable|exists:document_categories,id',
            'version' => 'required|string|max:20',
            'status' => 'required|in:draft,published,archived',
            'file' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            $path = $request->file('file')->store('documents', 'public');
            $validated['file_path'] = $path;
        }

        $document->update($validated);

        return redirect()->route('management.documents.index')->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        return redirect()->route('management.documents.index')->with('success', 'Document deleted successfully.');
    }
}
