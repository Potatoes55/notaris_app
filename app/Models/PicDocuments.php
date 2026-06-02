<?php

namespace App\Models;

use App\LogsActivityCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PicDocuments extends Model
{
    use LogsActivityCustom, SoftDeletes;
    protected $table = 'pic_documents';

    protected $fillable = [
        'notaris_id',
        'pic_document_code',
        'pic_id',
        'client_code',
        'transaction_id',
        'transaction_type',
        'received_date',
        'status',
        'note',
    ];

    public function pic()
    {
        return $this->belongsTo(PicStaff::class, 'pic_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_code', 'client_code');
    }

    public function notaris()
    {
        return $this->belongsTo(Notaris::class, 'notaris_id', 'id');
    }

    public function processes()
    {
        return $this->hasMany(PicProcess::class, 'pic_document_id');
    }

    public function latestProcess()
    {
        return $this->hasOne(PicProcess::class, 'pic_document_id')->latestOfMany('step_date');
    }

    public function aktaTransaction()
    {
        return $this->belongsTo(NotaryAktaTransaction::class, 'transaction_id', 'id');
    }

    public function relaasTransaction()
    {
        return $this->belongsTo(NotaryRelaasAkta::class, 'transaction_id', 'id');
    }

    public function prosesLain()
    {
        return $this->belongsTo(ProsesLain::class, 'transaction_id', 'id');
    }

public function getTransactionAttribute()
{
    switch ($this->transaction_type) {
        case 'akta': return $this->aktaTransaction;
        case 'ppat': return $this->relaasTransaction;
        case 'proses_lain': return $this->prosesLain;
        default: return null;
    }
}
}