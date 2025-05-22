<?php

namespace App\Http\Controllers;

use App\DataTables\ItemsDataTable;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB facade for transactions if needed

class ItemsController extends Controller
{
    public function index(ItemsDataTable $dataTable)
    {
        $categories = Item::query()->distinct()->pluck('category');
        $warehouses = Warehouse::all(['id', 'name']);

        return $dataTable->render('items.index', compact('categories', 'warehouses'));
    }

    public function create()
    {
        // This method might not be used if you only use the modal for creation
        return view('items.create');
    }

    public function store (Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouse,id',
            'status' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:1|max:255', 
            'notes' => 'nullable|string|max:255',
        ]);

        $status = $validatedData['status'] ?? 'available';
        $quantity = (int) $validatedData['quantity'];

            $item = Item::create([
                'name' => $validatedData['name'],
                'category' => $validatedData['category'],
            ]);

            $itemStocksData = [];
            for ($i = 0; $i < $quantity; $i++) {
                $itemStocksData[] = [
                    'item_id' => $item->id,
                    'warehouse_id' => $validatedData['warehouse_id'],
                    'status' => $status,
                    'notes' => $validatedData['notes'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            ItemStock::insert($itemStocksData);

        return redirect()->route('items.index')->with('success', 'Items created successfully!');
    }

    public function update(Request $request, $id) // Changed: $id is item_stock_id
    {
        $validatedItemData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        $validatedItemStockData = $request->validate([
            'warehouse_id' => 'required|exists:warehouse,id',
            'status' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        $itemStock = ItemStock::with('item')->findOrFail($id);

        // Update the parent Item
            $itemStock->item->update([
                'name' => $validatedItemData['name'],
                'category' => $validatedItemData['category'],
            ]);

        // Update the ItemStock
            $itemStock->update([
                'warehouse_id' => $validatedItemStockData['warehouse_id'],
                'status' => $validatedItemStockData['status'],
                'notes' => $validatedItemStockData['notes'] ?? null,
            ]);

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy($id) 
    {
        $itemStock = ItemStock::findOrFail($id);
        $itemId = $itemStock->item_id;

            $itemStock->delete();

            $remainingStocks = ItemStock::where('item_id', $itemId)->count();
            if ($remainingStocks === 0) {
                Item::destroy($itemId);
            }

        return response()->json(['message' => 'Item stock deleted successfully. Parent item checked and removed if orphaned.']);
    }
}
