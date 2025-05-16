<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'name',
        'category',
        'description',

    ];

    public function itemStocks(): HasMany
    {
        return $this->hasMany(ItemStock::class);
    
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
