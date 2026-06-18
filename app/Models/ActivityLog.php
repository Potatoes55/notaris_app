<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use SoftDeletes;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'menu',
        'action',
        'data_type',
        'data_id',
        'ip_address',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function causer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subject()
    {
        return $this->morphTo();
    }
}
