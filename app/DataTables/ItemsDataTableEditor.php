<?php

declare(strict_types=1);

namespace App\DataTables;

use App\Models\Item;
use App\Models\ItemStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTablesEditor;

class ItemsDataTableEditor extends DataTablesEditor
{
    protected $model = ItemStock::class;

    /**
     * Get create action validation rules.
     */
    public function createRules(): array
    {
        return [
            'name' => 'required|max:255',
            'category' => 'required|max:255',
            'warehouse_id' => 'required|numeric|max:255',
            'status' => 'sometimes|required|max:255',
            'notes' => 'sometimes|nullable|max:255',
        ];
    }

    /**
     * Get edit action validation rules.
     */
    public function editRules(Model $model): array
    {
        return [
            'warehouse_id' => 'required|numeric|max:255',
            'status' => 'sometimes|required|max:255',
            'notes' => 'sometimes|nullable|max:255',
            // You might want to allow editing name/category through a different mechanism
        ];
    }

    /**
     * Event hook that is fired before the model is created.
     */
    // public function creating(Model $model, array $data): array
    // {
    //     $item = Item::create([
    //         'name' => $data['name'],
    //         'category' => $data['category'],
    //     ]);

    //     $data['item_id'] = $item->id;
    //     unset($data['name']); // Remove name from item_stocks data
    //     unset($data['category']); // Remove category from item_stocks data

    //     return $data; // The $data array will now be used to create the ItemStock
    // }


    public function creating(Model $model, array $data): array
    {
        $quantity = (int) $data['quantity'];
        unset($data['quantity']); // Remove quantity from the $data array for ItemStock

        $item = Item::create([
            'name' => $data['name'],
            'category' => $data['category'],
        ]);

        $itemStocksData = [];
        for ($i = 0; $i < $quantity; $i++) {
            $itemStocksData[] = [
                'item_id' => $item->id,
                'warehouse_id' => $data['warehouse_id'],
                'status' => $data['status'], // Use default if not provided
                'notes' => $data['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        ItemStock::insert($itemStocksData);

        // Return the first ItemStock data or null
        return $itemStocksData[0] ?? []; // Or return null;
    }

    public function removeRules(Model $model): array
    {
        return [
            'DT_RowId' => 'required|not_in:'.auth()->id(),
        ];
    }

}