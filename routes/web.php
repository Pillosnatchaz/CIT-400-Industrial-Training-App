<?php

use App\Http\Controllers\UsersController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\ItemStocksController;
use App\Http\Controllers\WarehousesController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReceiptsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Displaying
Route::get('/users', [UsersController::class, 'index'])
    ->middleware('auth')
    ->name('users.index');

Route::get('/items', [ItemsController::class, 'index'])
    ->middleware('auth')
    ->name('items.index');

Route::get('/items/categories', [ItemsController::class, 'getCategories'])
    ->middleware('auth')
    ->name('items.get_categories');
    
Route::get('/warehouses', [WarehousesController::class, 'index'])
    ->middleware('auth')
    ->name('warehouse.index');
    
Route::get('/projects', [ProjectController::class, 'index'])
    ->middleware('auth')
    ->name('projects.index');

// Route::get('/receipts', [ReceiptsController::class, 'index'])
//     ->middleware('auth')
//     ->name('receipt.index');

Route::get('/receipt/checkin', [ReceiptsController::class, 'index'])
    ->middleware('auth')
    ->name('receipt.checkin');
    
Route::get('/receipt/checkout', [ReceiptsController::class, 'index'])
    ->middleware('auth')
    ->name('receipt.checkout');

//others
Route::resource('user', UsersController::class)
    ->middleware('auth');

Route::resource('items', ItemsController::class)
    ->middleware('auth');

Route::resource('warehouse', WarehousesController::class)
    ->middleware('auth');

Route::resource('project', ProjectController::class)
    ->middleware('auth');    

Route::resource('receipt', ReceiptsController::class)
    ->middleware('auth');
