<?php

namespace App\Models;

use App\LogsActivityCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PicStaff extends Model
{
    use LogsActivityCustom, SoftDeletes;

    protected $table = 'pic_staff';

    protected $fillable = [
        'notaris_id',
        'full_name',
        'email',
        'phone_number',
        'position',
        'address',
        'note'
    ];

    public function notaris()
    {
        return $this->belongsTo(Notaris::class);
    }

    protected $casts = [
        'email' => 'encrypted',
        'phone_number' => 'encrypted',
    ];
}
