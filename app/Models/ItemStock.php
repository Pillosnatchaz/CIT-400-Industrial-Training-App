<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class ItemStock extends Model
{
    use HasFactory;

    protected $table = 'item_stocks';

    protected $fillable = [
        'item_id',
        'warehouse_id',
        'status',
        'notes',
        'name',  
        'category',
    ];

    public function item(): belongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse(): belongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
