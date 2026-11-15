<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vpn extends Model
{
    use HasFactory;

    protected $fillable = [
        'vpn_name_id',
        'user_id',
        'expire_date',
        'user_name',
        'password',
        'link',
        'payment',
        'status',
        'buy_now_status',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'expire_date' => 'date',
            'payment' => 'decimal:2',
            'buy_now_status' => 'boolean',
            'purchased_at' => 'datetime',
            'password' => 'encrypted',
        ];
    }

    public function vpnName(): BelongsTo
    {
        return $this->belongsTo(VpnName::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
