<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\ProjectsDataTableEditor;
use App\DataTables\ProjectsDataTable;

class ProjectController extends Controller
{
    public function index(ProjectsDataTable $dataTable)
    {
        return $dataTable->render('projects.index');
    }

    public function store(ProjectsDataTableEditor $editor)
    {
        return $editor->process(request());
    }
}
