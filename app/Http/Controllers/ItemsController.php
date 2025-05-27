<?php

namespace App\Http\Controllers;

use App\DataTables\ItemsDataTable;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Warehouse;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
{
    public function index(ItemsDataTable $dataTable)
    {
        $itemNames = Item::query()->distinct()->pluck('name');
        $categories = Item::query()->distinct()->pluck('category');
        $warehouses = Warehouse::all(['id', 'name']);

        return $dataTable->render('items.index', compact('categories', 'warehouses', 'itemNames'));
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

            $item = Item::firstOrCreate([
                'name' => $validatedData['name'],
                'category' => $validatedData['category'],
            ]);

            $lastSku = ItemStock::where('item_id', $item->id)
                ->where('SKU', 'like', 'SKU-' . $item->id . '-%')
                ->orderByRaw("CAST(SUBSTRING_INDEX(SKU, '-', -1) AS UNSIGNED) DESC")
                ->value('SKU');

            $lastNumber = 0;
            if ($lastSku) {
                // Extract the last number from SKU (e.g., SKU-337-12 => 12)
                $parts = explode('-', $lastSku);
                $lastNumber = intval(end($parts));
            }

            $itemStocksData = [];
            for ($i = 1; $i <= $quantity; $i++) {
                $skuNumber = $lastNumber + $i;
                $sku = 'SKU-' . $item->id . '-' . $skuNumber;
                $itemStocksData[] = [
                    'item_id' => $item->id,
                    'warehouse_id' => $validatedData['warehouse_id'],
                    'status' => $status,
                    'notes' => $validatedData['notes'] ?? null,
                    'SKU' => $sku,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            ItemStock::insert($itemStocksData);

            $this->activityLog(
                'Item',
                $item->id,
                'created',
                "Item '{$item->name}' (Category: {$item->category}) created with {$quantity} stock unit(s) in Warehouse ID {$validatedData['warehouse_id']}."
            );

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

            $this->activityLog(
                'ItemStock', // Logging against ItemStock as it's the primary entity being identified by $id
                $itemStock->id,
                'updated',
                "ItemStock ID {$itemStock->id} updated. ".
                "Item: '{$itemStock->item->name}' (Category: {$itemStock->item->category}). ".
                "Stock Details: Status '{$itemStock->status}', Warehouse '{$itemStock->warehouse->name}', Notes: '{$itemStock->notes}'."
            );

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy($id) 
    {
        $itemStock = ItemStock::findOrFail($id);
        $itemId = $itemStock->item_id;
        $item = $itemStock->item; // Fetch before delete
        $warehouseName = $itemStock->warehouse ? $itemStock->warehouse->name : '';

        $itemStock->delete();

        $remainingStocks = ItemStock::where('item_id', $itemId)->count();
        if ($remainingStocks === 0) {
            Item::destroy($itemId);
        }

        $notes = "Item '{$item->name}' (Category: {$item->category}) deleted. Last stock was in Warehouse '{$warehouseName}'.";

        $this->activityLog(
            'Item',
            $item->id,
            'deleted',
            $notes
        );

        return response()->json(['message' => 'Item stock deleted successfully. Parent item checked and removed if orphaned.']);
    }

    public function activityLog(string $entityType, int $entityId, string $action, string $notes) 
    {
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'notes' => $notes,
            'performed_at' => now(),
        ]);
    }
}
