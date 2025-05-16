<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

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

    protected static function booted()
    {
        parent::boot();

        static::deleted(function ($model) {
            ActivityLog::create([
                'admin_id' => Auth::id(),
                'entity_type' => 'items',
                'entity_id' => $model->id,
                'action' => 'deleted',
                'notes' => 'Item Deleted: ' . $model->name, // Customize
            ]);
        });
    }
}
