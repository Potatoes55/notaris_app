<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaryAktaDocuments extends Model
{
    protected $table = 'notary_akta_documents';

    protected $fillable = [
        'notaris_id',
        'client_code',
        'akta_transaction_id',
        'name',
        'type',
        'file_name',
        'file_url',
        'file_type',
        'uploaded_at',
    ];

    public function notaris()
    {
        return $this->belongsTo(Notaris::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function akta_transaction()
    {
        return $this->belongsTo(NotaryAktaTransaction::class);
    }
}
