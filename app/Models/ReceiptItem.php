<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptItem extends Model
{
    use HasFactory;

    protected $table= 'receipt_item';

    protected $fillable = [
        'receipt_id',
        'item_stock_id',
        'status',
        'condition_in',
        'condition_out',
        'date_returned',
        'notes',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function item_stock()
    {
        return $this->belongsTo(\App\Models\ItemStock::class, 'item_stock_id');
    }
}
