<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ItemStock extends Model
{
    use HasFactory;

    protected $table = 'item_stocks';

    protected $fillable = [
        'item_id',
        'SKU',
        'warehouse_id',
        'status',
        'notes',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Item::class, 'item_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
    
}
