<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouse';

    protected $fillable = [
        'name',
        'address',
    ];

    public function itemStocks()
    {
        return $this->hasMany(ItemStock::class);
    }
}
