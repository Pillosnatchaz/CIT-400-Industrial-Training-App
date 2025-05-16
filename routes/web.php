<?php

use App\Http\Controllers\UsersController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\WarehousesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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


Route::get('/users', [UsersController::class, 'index'])->middleware('auth')->name('users.index');
Route::get('/items', [ItemsController::class, 'index'])->middleware('auth')->name('items.index');
Route::get('/warehouses', [WarehousesController::class, 'index'])->middleware('auth')->name('warehouse.index');

Route::post('/users', [UsersController::class, 'store'])->middleware('auth')->name('users.store');
Route::post('/items', [ItemsController::class, 'store'])->middleware('auth')->name('items.store');
Route::post('/warehouses', [WarehousesController::class, 'store'])->middleware('auth')->name('warehouse.store');
