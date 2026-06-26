<?php

namespace App\Models;

use App\LogsActivityCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaryRelaasAkta extends Model
{
    use LogsActivityCustom, SoftDeletes;

    protected $table = 'notary_relaas_aktas';

    protected $fillable = [
        'notaris_id',
        'client_code',
        'transaction_code',
        'year',
        'relaas_type_id',
        'relaas_number',
        'relaas_number_created_at',
        'title',
        'story',
        'story_date',
        'story_location',
        'status',
        'note',
    ];

    public function akta_type()
    {
        return $this->belongsTo(RelaasType::class, 'relaas_type_id');
    }

    public function notaris()
    {
        return $this->belongsTo(Notaris::class, 'notaris_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_code', 'client_code');
    }

    public function parties()
    {
        return $this->hasMany(NotaryRelaasParties::class, 'relaas_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(NotaryRelaasDocument::class, 'relaas_id', 'id');
    }

    protected $casts = [
        'relaas_number_created_at' => 'datetime',
        // 'relaas_number' => 'encrypted',
    ];
}
