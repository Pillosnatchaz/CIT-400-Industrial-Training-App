<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log';

    protected $fillable = [
        'admin_id',
        'entity_type',
        'entity_id',
        'action',
        'notes',
        'performed_at',
    ];
    
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
