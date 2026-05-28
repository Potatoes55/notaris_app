<?php

namespace App\Models;

use App\LogsActivityCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notaris extends Model
{
    use LogsActivityCustom, SoftDeletes;

    protected $table = 'notaris';

    protected $fillable = [
        'id',
        'user_id',
        'first_name',
        'last_name',
        'display_name',
        'office_name',
        'office_address',
        'image',
        'background',
        'address',
        'phone',
        'no_telp',
        'email',
        'gender',
        'information',
        'sk_ppat',
        'sk_ppat_date',
        'sk_notaris',
        'sk_notaris_date',
        'no_kta_ini',
        'no_kta_ippat',
        'provinsi_id',
        'provinsi_name',
        'kota_id',
        'kota_name',
        'kecamatan_id',
        'kecamatan_name',
        'kelurahan_id',
        'kelurahan_name',
    ];

    protected $casts = [
        'phone' => 'encrypted',
        'email' => 'encrypted',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class, 'notaris_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
