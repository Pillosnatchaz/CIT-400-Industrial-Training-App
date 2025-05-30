<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\DataTables\ProjectsDataTable;
use App\Http\Traits\LogsActivity;
use Illuminate\Http\JsonResponse; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProjectController extends Controller
{
    use LogsActivity;

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
            'name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'start_range' => 'required|max:255',
            'end_range' => 'required|date|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        if (isset($validatedData['start_range'])) {
            $dates = explode(',', $validatedData['start_range']);
            $trimmedDates = array_map('trim', $dates);
            $filteredDates = array_filter($trimmedDates);

            // $validatedData['start_range'] = json_encode($filteredDates);
            $validatedData['start_range'] = $filteredDates;
        }

        if (isset($validatedData['end_range'])) 
        {
            $validatedData['end_range'] = Carbon::parse($validatedData['end_range'])->format('Y-m-d H:i:s');
        }
        
        $validatedData['created_by'] = auth()->id();

        $project = Project::create($validatedData);

        $this->logActivity('Project', $project->id, 'created', ['data' => $validatedData]);

        return redirect()->route('project.index')->with('success','Project created successfully');
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
        $originalAttributes = $project->getOriginal();

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'client_name' => 'required|max:255',
            'start_range' => 'required|max:255',
            'end_range' => 'required|max:255',
            'location' => 'required|max:255',
            'description' => 'nullable|max:255',
        ]);

        if (isset($validatedData['start_range'])) {
            $dates = explode(',', $validatedData['start_range']);
            $trimmedDates = array_map('trim', $dates);
            $filteredDates = array_filter($trimmedDates);
            // $validatedData['start_range'] = json_encode($filteredDates);

            $validatedData['start_range'] = $filteredDates;
        }

        if (isset($validatedData['end_range']))
        {
            // Assuming your end_range is stored as Y-m-d H:i:s
            $validatedData['end_range'] = Carbon::parse($validatedData['end_range'])->format('Y-m-d H:i:s');
        }

        $project->update($validatedData);

        $this->logActivity('Project', $project->id, 'updated', [
            'old_attributes' => $originalAttributes,
            'new_attributes' => $project->getChanges() // This gives you only the attributes that changed, with their new values
        ]);

        return redirect()->route('project.index');
    }

    public function destroy (Project $project)
    {
        $deletedProjectData = $project->toArray();

        $project->delete();

        $this->logActivity('Project', $project->id, 'deleted', ['data' => $deletedProjectData]);

        return response()->json(['message' => 'Project deleted successfully']);

    }
}
