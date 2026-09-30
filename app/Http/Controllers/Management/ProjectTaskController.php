<?php

namespace App\Http\Controllers\Management;

use App\Domains\Core\Models\Project;
use App\Domains\Core\Models\ProjectTask;
use App\Domains\Core\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function create(Project $project)
    {
        $users = User::all();

        return view('management.projects.tasks.create', compact('project', 'users'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $project->tasks()->create($validated);

        return redirect()->route('management.projects.show', $project)->with('success', 'Task added successfully.');
    }

    public function edit(ProjectTask $task)
    {
        $task->load('project');
        $users = User::all();

        return view('management.projects.tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, ProjectTask $task)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return redirect()->route('management.projects.show', $task->project_id)->with('success', 'Task updated successfully.');
    }

    public function destroy(ProjectTask $task)
    {
        $projectId = $task->project_id;
        $task->delete();

        return redirect()->route('management.projects.show', $projectId)->with('success', 'Task deleted successfully.');
    }
}
