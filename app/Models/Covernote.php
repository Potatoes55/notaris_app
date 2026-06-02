<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Covernote extends Model
{
    use HasFactory;

    protected $fillable = [
        'notaris_id',
        'client_id',
        'client_code',
        'covernote_number',
        'recipient',
        'subject',
        'date',
        'expiry_date',
        'attachment',
        'file_path',
    ];

    /**
     * Relasi ke data Klien
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}