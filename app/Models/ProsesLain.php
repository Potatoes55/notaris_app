<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProsesLain extends Model
{
    protected $table = 'proses_lain';

    protected $fillable = [
        'client_code',
        'notaris_id',
        'pic_id',
        'name',
        'transaction_code',
        'time_estimation',
        'status'
    ];

    public function notaris()
    {
        return $this->belongsTo(Notaris::class, 'notaris_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_code', 'client_code');
    }

    public function picStaff()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function picDocument()
    {
        return $this->belongsTo(PicDocuments::class, 'client_code', 'client_code');
    }
}