<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\ProjectsDataTable;
use App\Models\Project;
use Illuminate\Http\JsonResponse; 

class ProjectController extends Controller
{
    public function index(ProjectsDataTable $dataTable)
    {
        return $dataTable->render('projects.index');
    }

    public function create()
    {
        return view('project.create');
    }

    public function store (Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'client_name' => 'required|max:255',
            'start_range' => 'required|max:255',
            'end_range' => 'required|max:255',
            'location' => 'required|max:255',
            'description' => 'nullable|max:255',
        ]);

        $validatedData['created_by'] = auth()->id();

        Project::create($validatedData);

        return redirect()->route('project.index');
    }

    public function edit (Project $project) 
    {
        return view('project.edit', compact('project'));
    }

    public function show (Project $project)
    {
        return view('project.show', compact('project'));
    }

    public function update (Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'client_name' => 'required|max:255',
            'start_range' => 'required|max:255',
            'end_range' => 'required|max:255',
            'location' => 'required|max:255',
            'description' => 'nullable|max:255',
        ]);

        $project->update($validatedData);

        return redirect()->route('project.index');
    }

    public function destroy (Project $project)
    {
        $project->delete();

        // return redirect()->route('project.index');
        return response()->json(['message' => 'Project deleted successfully']); // Return a JSON response

    }
}
