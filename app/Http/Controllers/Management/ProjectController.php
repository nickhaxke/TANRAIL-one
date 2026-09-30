<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::with('businessUnit')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('management.projects.index', compact('projects'));
    }

    public function create()
    {
        $businessUnits = BusinessUnit::where('status', true)->get();

        return view('management.projects.create', compact('businessUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_unit_id' => 'required|exists:business_units,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:projects,code',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,active,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'numeric|min:0',
        ]);

        Project::create($validated);

        return redirect()->route('management.projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['businessUnit', 'tasks.assignee']);

        return view('management.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $businessUnits = BusinessUnit::where('status', true)->get();

        return view('management.projects.edit', compact('project', 'businessUnits'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'business_unit_id' => 'required|exists:business_units,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:projects,code,'.$project->id,
            'description' => 'nullable|string',
            'status' => 'required|in:planned,active,completed,cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'numeric|min:0',
        ]);

        $project->update($validated);

        return redirect()->route('management.projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('management.projects.index')->with('success', 'Project deleted successfully.');
    }
}
