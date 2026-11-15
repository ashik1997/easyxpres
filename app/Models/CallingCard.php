<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CallingCard extends Model
{
      use HasFactory;

    protected $table = 'calling_cards';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'account_no',
        'card_no',
        'user_name',
        'pin',
        'point',
        'minute_sec',
        'payment',
        'status',
        'buy_now_status',
    ];

    /**
     * Optional: define relation to user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
