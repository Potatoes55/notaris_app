<?php

namespace App\Models;

use App\LogsActivityCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use LogsActivityCustom, SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'client_code',
        'notaris_id',
        'uuid',
        'fullname',
        'nik',
        'birth_place',
        'gender',
        'marital_status',
        'job',
        'address',
        'kelurahan_id',
        'kelurahan_name',
        'kecamatan_id',
        'kecamatan_name',
        'province_id',
        'province_name',
        'postcode',
        'phone',
        'email',
        'npwp',
        'type',
        'company_name',
        'status',
        'note',
        // Company
        'legal_status',
        'business_form',
        'deed_number',
        'deed_date',
        'nib',
        // 'npwp',
        'pic_name',
        'pic_position',
        'pic_phone',
        'pic_email',
        // 'address',
        // 'city',
        // 'province',
        // 'postal_code',
        // 'company_phone',
        // 'company_email',
    ];

    public function notaris()
    {
        return $this->belongsTo(Notaris::class, 'notaris_id', 'id');
    }

    public function aktaTransactions()
    {
        return $this->hasMany(NotaryAktaTransaction::class, 'client_code', 'client_code');
    }

    public function aktaTransactionsRelaas()
    {
        return $this->hasMany(NotaryRelaasAkta::class, 'client_code', 'client_code');
    }

    protected $casts = [
        'phone' => 'encrypted',
        'email' => 'encrypted',
        'nik' => 'encrypted',
        'npwp' => 'encrypted',
    ];
}
