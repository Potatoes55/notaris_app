<?php

namespace App\Models;

use App\LogsActivityCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaryLetters extends Model
{
    protected $table = 'notary_letters';

    use LogsActivityCustom, SoftDeletes;

    protected $fillable = [
        'notaris_id',
        'client_code',
        'letter_number',
        'letter_type',
        'type',
        'recipient',
        'subject',
        'date',
        'summary',
        'attachment',
        'notes',
        'file_path',
    ];

    public function notaris()
    {
        return $this->belongsTo(Notaris::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_code', 'client_code');
    }

    protected $casts = [
        'letter_number' => 'encrypted',
    ];
}
