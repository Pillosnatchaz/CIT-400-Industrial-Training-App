<?php

namespace App\Http\Controllers;

use App\DataTables\WarehousesDataTable;
use App\Models\Warehouse;
use Illuminate\Http\Request;


class WarehousesController extends Controller
{
    public function index(WarehousesDataTable $dataTable)
    {
        return $dataTable->render('warehouse.index');
    }

    public function create()
    {
        return view('warehouse.create');
    }

    public function store (Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'address' => 'required|max:255',
        ]);

        Warehouse::create($validatedData);

        return redirect()->route('warehouse.index');
    }

    public function edit (Warehouse $warehouse) 
    {
        return view('warehouse.edit', compact('warehouse'));
    }

    public function show (Warehouse $warehouse)
    {
        return view('warehouse.show', compact('warehouse'));
    }

    public function update (Request $request, Warehouse $warehouse)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'address' => 'required|max:255',
        ]);

        $warehouse->update($validatedData);

        return redirect()->route('warehouse.index');
    }

    public function destroy (Warehouse $warehouse)
    {
        $warehouse->delete();

        // return redirect()->route('warehouse.index');
        return response()->json(['message' => 'Warehouse deleted successfully']); // Return a JSON response

    }
}
