<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/items/search', function (\Illuminate\Http\Request $request) {
//     $q = $request->get('q');
//     $category = $request->get('category');
//     $query = \App\Models\Item::query();
//     if ($q) {
//         $query->where('name', 'like', "%$q%");
//     }
//     if ($category) {
//         $query->where('category', $category);
//     }
//     return $query->limit(10)->get(['id', 'name', 'category']);
// });


Route::get('/items/search', function (\Illuminate\Http\Request $request) {
    $q = $request->get('q');
    $category = $request->get('category');

    $query = \App\Models\Item::query()
        ->select('items.*', DB::raw('(SELECT COUNT(*) FROM item_stocks WHERE item_id = items.id AND status = "available") as available_stock'));

    if ($q) {
        $query->where('name', 'like', "%$q%");
    }
    if ($category) {
        $query->where('category', $category);
    }

    return $query->limit(10)->get();
});