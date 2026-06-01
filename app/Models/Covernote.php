<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Covernote extends Model
{
    use HasFactory;

    protected $fillable = [
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