<?php

namespace App\Http\Controllers;

use App\DataTables\ItemsDataTable;
use App\DataTables\ItemsDataTableEditor;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function index(ItemsDataTable $dataTable)
    {
        return $dataTable->render('items.index');
    }

    public function store(ItemsDataTableEditor $editor)
    {
        return $editor->process(request());
    }
}
