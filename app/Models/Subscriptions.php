<?php

namespace App\Models;

use App\LogsActivityCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriptions extends Model
{
    use LogsActivityCustom, SoftDeletes;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'payment_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plans::class, 'plan_id');
    }
}
