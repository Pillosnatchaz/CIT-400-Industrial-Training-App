<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory;

    protected $table= 'receipt';

    protected $fillable = [
        'receipt_number',
        'user_id',
        'borrower_user_id',
        'parent_checkout_receipt_id',
        'project_id',
        'type',
        'expected_return_date',
        'actual_return_date',
        'status',
        'notes',
        'created_by', //unecessary drop later
    ];

    protected $casts = [
        'expected_return_date' => 'datetime',
        'actual_return_date' => 'datetime',
        'items' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function receipt_items()
    {
        return $this->hasMany(\App\Models\ReceiptItem::class, 'receipt_id');
    }

    public function items()
    {
        return $this->hasMany(\App\Models\ReceiptItem::class, 'receipt_id');
    }

    public function borrower()
    {
        return $this->belongsTo(\App\Models\User::class, 'borrower_user_id');
    }

    public function warehouse() 
    {
        return $this->belongsTo(\App\Models\Warehouse::class);
    }

}
