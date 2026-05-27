<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\LogsActivityCustom;

class NotaryClientWarkah extends Model
{
    use SoftDeletes, LogsActivityCustom;

    protected $table = 'notary_client_warkahs';

    protected $fillable = [
        'notaris_id',
        'client_code',
        'warkah_code',
        'warkah_name',
        'city',
        'note',
        'warkah_link',
        'uploaded_at',
        'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function notaris()
    {
        return $this->belongsTo(Notaris::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_code', 'client_code');
    }
}
