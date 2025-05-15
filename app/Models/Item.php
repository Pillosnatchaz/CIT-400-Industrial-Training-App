<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

}
