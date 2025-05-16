<?php

namespace App\Http\Controllers;

use App\DataTables\WarehousesDataTable;
use App\DataTables\WarehousesDataTableEditor;
use Illuminate\Http\Request;


class WarehousesController extends Controller
{
    public function index(WarehousesDataTable $dataTable)
    {
        return $dataTable->render('warehouse.index');
    }

    public function store(WarehousesDataTableEditor $editor)
    {
        return $editor->process(request());
    }
}
