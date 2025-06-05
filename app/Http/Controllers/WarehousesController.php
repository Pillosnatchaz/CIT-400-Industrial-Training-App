<?php

namespace App\Http\Controllers;

use App\DataTables\WarehousesDataTable;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;

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

        $this->logActivity('Warehouse', $warehouse->id, 'created', ['data' => $validatedData]);

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
        $originalAttributes = $user->getOriginal();

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'address' => 'required|max:255',
        ]);

        $warehouse->update($validatedData);

        $this->logActivity('Warehouse', $warehouse->id, 'updated', [
            'old_attributes' => $originalAttributes,
            'new_attributes' => $warehouse->getChanges() // This gives you only the attributes that changed, with their new values
        ]);

        return redirect()->route('warehouse.index');
    }

    public function destroy (Warehouse $warehouse)
    {
        $deletedWarehouseData = $warehouse->getOriginal();

        $warehouse->delete();

        $this->logActivity('Warehouse', $warehouse->id, 'deleted', ['data' => $deletedWarehouseData]);

        // return redirect()->route('warehouse.index');
        return response()->json(['message' => 'Warehouse deleted successfully']); // Return a JSON response

    }
}
