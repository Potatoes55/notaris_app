<?php

namespace App\Models;

use App\LogsActivityCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaryAktaTransaction extends Model
{

    use SoftDeletes, LogsActivityCustom;
    protected $table = 'notary_akta_transactions';

    protected $fillable = [
        'notaris_id',
        'client_code',
        'akta_type_id',
        'transaction_code',
        'year',
        'status',
        'akta_number',
        'akta_number_created_at',
        'date_submission',
        'date_finished',
        'note',
        // 'created_at',
        // 'updated_at'
    ];

    public function notaris()
    {
        return $this->belongsTo(Notaris::class);
    }

    public function akta_type()
    {
        return $this->belongsTo(NotaryAktaTypes::class, 'akta_type_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_code', 'client_code')
            ->withTrashed();
    }

    protected $casts = [
        'akta_number_created_at' => 'datetime',
        // 'akta_number' => 'encrypted',
    ];
}
