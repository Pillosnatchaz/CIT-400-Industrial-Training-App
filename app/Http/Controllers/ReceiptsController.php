<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\ReceiptsDataTable;
use App\DataTables\ReceiptsDataTableEditor;
use App\Models\Receipt;
use App\Models\Project;
use App\Models\User;
use App\Models\Item;
use App\Http\Traits\LogsActivity;
use Carbon\carbon;

class ReceiptsController extends Controller
{
    public function index(Request $request, ReceiptsDataTable $dataTable) 
    {
        $type = str_contains($request->route()->getName(), 'checkout') ? 'checkout' : 'checkin';

        $projects = Project::all();
        $borrowerID = User::all();
        $currentUserId = auth()->id();
        $categories = Item::distinct()->pluck('category');

        return $dataTable
            ->with(['type' => $type])
            ->render('receipt.index', compact('projects', 'borrowerID', 'currentUserId', 'categories') + [
                'receiptType' => $type
        ]);
        // return $dataTable->render('receipt.index', compact('projects', 'borrowerID', 'currentUserId', 'categories'));
    }

    public function create() 
    {
        return view('receipt.index');
    }

    public function store(Request $request)
    {
        // \Log::info('Receipt store request', $request->all());

        $validatedData = $request->validate([
            'borrower_user_id' => 'required|string|max:255',
            'parent_checkout_receipt_id' => 'nullable|string|max:255',
            'project_id' => 'required|string|max:255',
            'type' => 'required|in:checkout,checkin',
            'expected_return_date' => 'required|string|max:255|date',
            'actual_return_date' => 'nullable   |string|max:255|date',
            'status' => 'nullable|string|max:255',  
            'items' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $validatedData['created_by'] = auth()->id();
        $status = $validatedData['status'] ?? 'checked_out';
        // $prefix = $validatedData['type'] === 'checkin' ? 'CI' : 'CO';
        
        // Find the latest receipt_number for this project and type
        $checkoutPrefix = 'CO';
        $lastCheckoutReceipt = Receipt::where('project_id', $validatedData['project_id'])
            ->where('type', 'checkout')
            ->orderByRaw("CAST(SUBSTRING(receipt_number, LOCATE('-', receipt_number) + 1) AS UNSIGNED) DESC") // Adjust to parse number after prefix-project_id
            ->first();

        $lastCheckoutNumber = 0;
        if ($lastCheckoutReceipt && preg_match('/-(\d+)$/', $lastCheckoutReceipt->receipt_number, $matches)) {
            $lastCheckoutNumber = intval($matches[1]);
        }

        $nextCheckoutNumber = $lastCheckoutNumber + 1;
        $checkoutReceiptNumber = $checkoutPrefix . '-' . $validatedData['project_id'] . str_pad($nextCheckoutNumber, 2, '0', STR_PAD_LEFT);

        $expectedReturnDate = $request->input('expected_return_date');
        if ($expectedReturnDate) {
            $expectedReturnDate = Carbon::parse($expectedReturnDate)->format('Y-m-d H:i:s');
        }

        $checkoutReceipt  = Receipt::create([
            'receipt_number' => $checkoutReceiptNumber,
            'user_id' => auth()->id(),
            'borrower_user_id' => $validatedData['borrower_user_id'],
            'project_id' => $validatedData['project_id'],
            'type' => 'checkout',
            'expected_return_date' => $expectedReturnDate,
            'status' => $status,
            'created_by' => auth()->id(),
            'items' => $validatedData['items'],
            'notes' => $validatedData['notes']
        ]);

        $items = json_decode($request->input('items'), true);
        foreach ($items as $item) {
            // Find N available item_stocks for this item
            $availableStocks = \App\Models\ItemStock::where('item_id', $item['id'])
                ->where('status', 'available')
                ->limit($item['quantity'])
                ->get();

            if ($availableStocks->count() < $item['quantity']) {
                // Not enough stock, handle error as needed
                throw new \Exception("Not enough stock for {$item['name']}");
            }

            foreach ($availableStocks as $stock) {
                \App\Models\ReceiptItem::create([
                    'receipt_id' => $checkoutReceipt->id,
                    'item_stock_id' => $stock->id,
                    'status' => 'checked_out',
                    
                ]);
                
                $stock->update(['status' => 'in use']);
            }
        }

        $checkinPrefix = 'CI';
        $checkinReceiptNumber = $checkinPrefix . '-' . $validatedData['project_id'] . str_pad($nextCheckoutNumber, 2, '0', STR_PAD_LEFT);

        $checkinReceipt = Receipt::create([
            'receipt_number' => $checkinReceiptNumber,
            'user_id' => auth()->id(),
            'borrower_user_id' => $validatedData['borrower_user_id'],
            'project_id' => $validatedData['project_id'],
            'type' => 'checkin',
            'expected_return_date' => $expectedReturnDate,
            'status' => $status,
            'created_by' => auth()->id(),
            'items' => $validatedData['items'],
            'notes' => $validatedData['notes']

        ]);

        $this->logActivity('Receipt', $receipt->id, 'created', ['data' => $validatedData]);

        return redirect()->route('receipt.index')->with('success', 'Receipt created.');        
    }

    public function update(Request $request, Receipt $receipt)
    {
        $originalAttributes = $receipt->getOriginal();

        $validated = $request->validate([
            'borrower_user_id' => 'required|string|max:255',
            'parent_checkout_receipt_id' => 'nullable|string|max:255',
            'project_id' => 'required|string|max:255',
            'type' => 'required|in:checkout,checkout',
            'expected_return_date' => 'required|string|max:255|date',
            'actual_return_date' => 'nullable   |string|max:255|date',
            'status' => 'required|string|max:255',
            'created_by' => 'required|string|max:255',
        ]);

        $receipt->update([
            'borrower_user_id' => $validatedData['borrower_user_id'],
            'parent_checkout_receipt_id' => $validatedData['parent_checkout_receipt_id'],
            'project_id' => $validatedData['project_id'],
            'type' => $validatedData['type'],
            'expected_return_date' => $validatedData['expected_return_date'],
            'actual_return_date' => $validatedData['actual_return_date'],
            'status' => $validatedData['status'],
            $validatedData['created_by'] = auth()->id(),
        ]);

        $receipt->update($validatedData);

        $this->logActivity('Receipt', $receipt->id, 'updated', [
            'old_attributes' => $originalAttributes,
            'new_attributes' => $receipt->getChanges() 
        ]);

        return redirect()->route('receipt.index');
    }


    public function destroy(Receipt $receipt)
    {
        $deletedReceiptData = $receipt->toArray();
        
        // $receipt = Receipt::findOrFail($id);
        $receipt->delete(); 

        $this->logActivity('Receipt', $receipt->id, 'deleted', ['data' => $deletedReceiptData]);

        return response()->json(['message' => 'Receipt and items deleted.']);
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

    public function show($id)
    {
        $receipt = Receipt::with([
            // 'receipt_items.itemStock.item',
            // 'receipt_items.itemStock.warehouse',
            'receipt_items.item_stock.item',
            'receipt_items.item_stock.warehouse',
            'project',
            'warehouse',
            'user',
            'borrower'
        ])->findOrFail($id);

        $receipt->formatted_created_at = \Carbon\Carbon::parse($receipt->created_at)->format('d M Y H:i');
        $receipt->formatted_expected_return_date = \Carbon\Carbon::parse($receipt->expected_return_date)->format('d M Y');

        $groupedItems = collect($receipt->receipt_items)
            ->groupBy(function($item) {
                // Group by a unique string of all relevant fields
                return $item->item_stock->item->name . '|' .
                       $item->item_stock->warehouse->name . '|' .
                       $item->item_stock->warehouse->address;
            })
            ->map(function($group) {
                $first = $group->first();
                return [
                    'item_name' => $first->item_stock->item->name,
                    'warehouse_name' => $first->item_stock->warehouse->name,
                    'warehouse_address' => $first->item_stock->warehouse->address,
                    'quantity' => $group->count()
                ];
            })->values();

        $receipt->grouped_items = $groupedItems;

        return response()->json($receipt);
    }
}
