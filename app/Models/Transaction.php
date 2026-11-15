<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'transaction_id',
        'type',
        'amount',
        'balance_after',
        'note',
        'file_upload',
        'status',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Account::class,
            'id',
            'id',
            'account_id',
            'user_id'
        );
    }
}
